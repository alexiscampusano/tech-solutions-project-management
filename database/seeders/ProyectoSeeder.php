<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\User;
use Carbon\Carbon;

class ProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = User::all();
        
        if ($usuarios->isEmpty()) {
            throw new \Exception('No hay usuarios disponibles. Asegúrate de que el DatabaseSeeder cree usuarios primero.');
        }

        $proyectos = [
            [
                'nombre' => 'Sistema de Gestión de Inventario',
                'fecha_inicio' => Carbon::now()->subDays(30),
                'estado' => 'en_progreso',
                'responsable' => 'Ana García',
                'monto' => 2500000.00,
                'created_by' => $usuarios->get(0)->id, 
            ],
            [
                'nombre' => 'Aplicación Móvil de Ventas',
                'fecha_inicio' => Carbon::now()->subDays(15),
                'estado' => 'completado',
                'responsable' => 'Carlos Rodriguez',
                'monto' => 4800000.00,
                'created_by' => $usuarios->get(1)->id, 
            ],
            [
                'nombre' => 'Portal Web Corporativo',
                'fecha_inicio' => Carbon::now()->addDays(5),
                'estado' => 'iniciado',
                'responsable' => 'María López',
                'monto' => 3200000.00,
                'created_by' => $usuarios->get(2)->id, 
            ],
            [
                'nombre' => 'Sistema de Facturación Electrónica',
                'fecha_inicio' => Carbon::now()->subDays(45),
                'estado' => 'completado',
                'responsable' => 'Pedro Martínez',
                'monto' => 5600000.00,
                'created_by' => $usuarios->get(0)->id, 
            ],
            [
                'nombre' => 'Plataforma de E-learning',
                'fecha_inicio' => Carbon::now()->subDays(10),
                'estado' => 'en_progreso',
                'responsable' => 'Laura Sánchez',
                'monto' => 7200000.00,
                'created_by' => $usuarios->get(3)->id, 
            ],
            [
                'nombre' => 'API de Integración CRM',
                'fecha_inicio' => Carbon::now()->addDays(20),
                'estado' => 'iniciado',
                'responsable' => 'Roberto Silva',
                'monto' => 1800000.00,
                'created_by' => $usuarios->get(4)->id, 
            ],
            [
                'nombre' => 'Sistema de Monitoreo IoT',
                'fecha_inicio' => Carbon::now()->subDays(60),
                'estado' => 'cancelado',
                'responsable' => 'Sofía Mendoza',
                'monto' => 8900000.00,
                'created_by' => $usuarios->get(1)->id, 
            ],
            [
                'nombre' => 'Dashboard Ejecutivo BI',
                'fecha_inicio' => Carbon::now()->subDays(20),
                'estado' => 'en_progreso',
                'responsable' => 'Diego Herrera',
                'monto' => 4500000.00,
                'created_by' => $usuarios->get(2)->id, 
            ]
        ];

        foreach ($proyectos as $proyecto) {
            Proyecto::create($proyecto);
        }
    }
}
