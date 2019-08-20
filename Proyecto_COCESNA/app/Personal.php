<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    //se relaciona con la tabla personal
    protected $table = 'personal';
    
    //desactivar por que no tenemos las tablas created_at, etc
    public $timestamps = false;
    
    //las columnas se llenaran de forma aleatoria
    protected $fillable = [
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'fecha_ingreso',
        'sexo',
        'no_empleado',
        'contrasena',
        'email',
    ];

    //definir las relaciones con otras tablas
    public function posicion()
    {
        return $this->belongsTo('App\Posicion');   //relacion muchos a uno
    }

    public function clases_x_alumno()
    {
        return $this->belongsTo('App\Turno');   //relacion muchos a uno
    }
    
}
