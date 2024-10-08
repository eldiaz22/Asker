<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;




class AdmController extends Controller
{
    public function index()
{
    // Cargar todos los usuarios directamente de la tabla
    $users = User::all(); // Asume que los roles están en la misma tabla de usuarios
    $mesas = Mesa::all();
    $user = Auth::user(); // Obtiene el usuario 

    $pedidos = Pedido::with('detalles.producto', 'mesa', 'usuario')
    ->where('estado', 'pending')
    ->get();

    $totalMesas = $mesas->count();
    return view('admin.index', compact('users', "totalMesas",  "mesas",  "pedidos", "user"));
}


// Mostrar el formulario de creación de usuario
public function create(){
$roles = User::select('role')->distinct()->get(); 

    return view('admin.create', compact("roles"));
}

// Almacenar un nuevo usuario
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'role' => 'required|string|max:50',
        'password' => 'required|string|min:8|confirmed', // Validación para la contraseña
    ]);

    // Crear el usuario con la contraseña encriptada
    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'role' => $validated['role'],
        'password' => Hash::make($validated['password']),
    ]);

    return redirect()->route('admin.index')->with('success', 'Usuario creado exitosamente.');
}

// Mostrar el formulario de edición de usuario
public function edit($id)
{
    $user = User::findOrFail($id);

    // Define los roles disponibles
    $roles = ['admin', 'chef', 'user']; // Ajusta estos roles según sea necesario

    return view('admin.edit', compact('user', 'roles'));
}

// Actualizar un usuario existente
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        'role' => 'required|string|max:50',
        'password' => 'nullable|string|min:8|confirmed', // Validación para la contraseña
    ]);

    $user = User::findOrFail($id);
    
    // Actualiza los datos del usuario
    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];

    // Actualiza la contraseña solo si se ha proporcionado una nueva
    if ($request->filled('password')) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()->route('admin.index')->with('success', 'Usuario actualizado exitosamente.');
}


// Eliminar un usuario
public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('admin.index')->with('success', 'Usuario eliminado exitosamente.');
}
}



