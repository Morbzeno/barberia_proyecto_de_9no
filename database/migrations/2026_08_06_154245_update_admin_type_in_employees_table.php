<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE employees_new (
                employeeID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                userID INTEGER NOT NULL,
                personID INTEGER NOT NULL,
                payment NUMERIC NOT NULL,
                schedule TEXT NOT NULL,
                rfc VARCHAR NOT NULL,
                admin_type VARCHAR NOT NULL
                    CHECK(admin_type IN ("barber", "receptionist", "admin"))
                    DEFAULT "barber",
                deleted_at DATETIME,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY(userID)
                    REFERENCES users(userID)
                    ON DELETE CASCADE,
                FOREIGN KEY(personID)
                    REFERENCES persons(personID)
                    ON DELETE CASCADE
            )
        ');

        DB::statement('
            INSERT INTO employees_new (
                employeeID,
                userID,
                personID,
                payment,
                schedule,
                rfc,
                admin_type,
                deleted_at,
                created_at,
                updated_at
            )
            SELECT
                employeeID,
                userID,
                personID,
                payment,
                schedule,
                rfc,
                admin_type,
                deleted_at,
                created_at,
                updated_at
            FROM employees
        ');

        DB::statement('DROP TABLE employees');

        DB::statement(
            'ALTER TABLE employees_new RENAME TO employees'
        );

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE employees_old (
                employeeID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                userID INTEGER NOT NULL,
                personID INTEGER NOT NULL,
                payment NUMERIC NOT NULL,
                schedule TEXT NOT NULL,
                rfc VARCHAR NOT NULL,
                admin_type VARCHAR NOT NULL
                    CHECK(admin_type IN ("barber", "admin"))
                    DEFAULT "barber",
                deleted_at DATETIME,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY(userID)
                    REFERENCES users(userID)
                    ON DELETE CASCADE,
                FOREIGN KEY(personID)
                    REFERENCES persons(personID)
                    ON DELETE CASCADE
            )
        ');

        DB::statement('
            INSERT INTO employees_old (
                employeeID,
                userID,
                personID,
                payment,
                schedule,
                rfc,
                admin_type,
                deleted_at,
                created_at,
                updated_at
            )
            SELECT
                employeeID,
                userID,
                personID,
                payment,
                schedule,
                rfc,
                CASE
                    WHEN admin_type = "receptionist"
                    THEN "admin"
                    ELSE admin_type
                END,
                deleted_at,
                created_at,
                updated_at
            FROM employees
        ');

        DB::statement('DROP TABLE employees');

        DB::statement(
            'ALTER TABLE employees_old RENAME TO employees'
        );

        DB::statement('PRAGMA foreign_keys = ON');
    }
};