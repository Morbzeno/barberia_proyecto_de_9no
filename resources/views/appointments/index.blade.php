@php
    // Definimos el rango de la jornada laboral
    $horaInicioJornada = 8;  // 08:00 AM
    $horaFinJornada = 20;   // 08:00 PM
    $totalMinutosJornada = ($horaFinJornada - $horaInicioJornada) * 60;
@endphp

<div id="timeline-container" style="background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 32px; border: 1px solid #e5e7eb; font-family: sans-serif;">
    <h2 style="font-size: 18px; font-weight: bold; color: #374151; margin-bottom: 16px; margin-top: 0;">Ocupación del Día (Línea de Tiempo)</h2>
    
    <div style="position: relative; width: 100%; background-color: #e5e7eb; height: 48px; border-radius: 8px; margin-bottom: 10px; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
        
        <div style="position: absolute; width: 100%; height: 100%; display: flex; justify-content: space-between; box-sizing: border-box; padding: 0 10px; pointer-events: none;">
            @for ($i = $horaInicioJornada; $i <= $horaFinJornada; $i++)
                <div style="border-left: 1px solid #d1d5db; height: 100%; display: flex; flex-direction: column; justify-content: space-between; padding-top: 4px; padding-bottom: 4px; box-sizing: border-box;">
                    <span style="font-size: 10px; color: #9ca3af; font-weight: 500;">{{ sprintf('%02d:00', $i) }}</span>
                </div>
            @endfor
        </div>

        @foreach($appointments as $appointment)
            @php
                $appt = is_array($appointment) ? $appointment : json_decode(json_encode($appointment), true);
                
                $start = \Carbon\Carbon::parse($appt['startHour']);
                $finish = \Carbon\Carbon::parse($appt['finishHour']);
                
                $minutosDesdeInicio = ($start->hour - $horaInicioJornada) * 60 + $start->minute;
                $duracionCita = $start->diffInMinutes($finish);
                
                $posicionIzquierda = ($minutosDesdeInicio / $totalMinutosJornada) * 100;
                $anchoBloque = ($duracionCita / $totalMinutosJornada) * 100;
                
                $posicionIzquierda = max(0, min(100, $posicionIzquierda));
                $anchoBloque = max(2.0, min(100 - $posicionIzquierda, $anchoBloque)); // 2% mínimo para que sea clickeable/visible
            @endphp

           <div class="timeline-block" 
     style="position: absolute; top: 8px; height: 32px; background-color: #6366f1; color: #ffffff; font-size: 11px; font-weight: bold; border-radius: 4px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; --left-pos: {{ $posicionIzquierda }}%; --width-pos: {{ $anchoBloque }}%; left: var(--left-pos); width: var(--width-pos);"
     title="Cita #{{ $appt['appointmentID'] }} ({{ $start->format('H:i') }} a {{ $finish->format('H:i') }}) - Estado: {{ $appt['status'] }}">
                
                <span>#{{ $appt['appointmentID'] }}</span>
            </div>
        @endforeach
    </div>
    
    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; margin-top: 8px;">
        <span>Inicio jornada: {{ sprintf('%02d:00', $horaInicioJornada) }}</span>
        <span>Fin jornada: {{ sprintf('%02d:00', $horaFinJornada) }}</span>
    </div>
</div>