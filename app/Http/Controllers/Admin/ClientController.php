<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::with(['user', 'person'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->whereHas('person', fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')
                    ->orWhere('last_name', 'like', '%'.$request->q.'%'));
            })
            ->orderBy('clientID', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $person = Person::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
            ]);

            Client::create([
                'userID' => $user->userID,
                'personID' => $person->personID,
            ]);
        });

        return redirect()->route('admin.clients.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Client $client)
    {
        $client->load(['user', 'person']);

        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $client->load(['user', 'person']);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($client->userID, 'userID')],
            'password' => 'nullable|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10',
        ]);

        DB::transaction(function () use ($data, $client) {
            $userData = ['email' => $data['email']];
            if (!empty($data['password'])) {
                $userData['password'] = bcrypt($data['password']);
            }
            $client->user->update($userData);

            $client->person->update([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
            ]);
        });

        return redirect()->route('admin.clients.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        if ($client->appointments()->exists()) {
            return back()->with('error', 'No puedes eliminar un cliente con citas asociadas.');
        }

        DB::transaction(function () use ($client) {
            $client->delete();
            $client->user?->delete();
            $client->person?->delete();
        });

        return redirect()->route('admin.clients.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
