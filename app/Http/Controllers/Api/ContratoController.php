<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContratoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Contrato::with(['empleado', 'departamento', 'cargo']);

        if ($request->filled('empleado_id')) {
            $query->where('IDEmpleado', $request->empleado_id);
        }

        if ($request->filled('estado')) {
            $query->where('Estado', $request->estado);
        }

        $contratos = $query->orderBy('FechaInicio', 'desc')
                          ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => $contratos,
            'message' => 'Contratos obtenidos exitosamente'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDEmpleado' => ['required', 'integer', 'exists:empleados,IDEmpleado'],
            'IDDepartamento' => ['required', 'integer', 'exists:departamentos,IDDepartamento'],
            'IDCargo' => ['required', 'integer', 'exists:cargos,IDCargo'],
            'NumeroContrato' => ['required', 'string', 'max:50', 'unique:contratos,NumeroContrato'],
            'FechaInicio' => ['required', 'date'],
            'FechaFin' => ['nullable', 'date', 'after:FechaInicio'],
            'HaberBasico' => ['required', 'numeric', 'min:2500'],
            'Observaciones' => ['nullable', 'string', 'max:500']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => -1,
                'error' => 'Datos de entrada inválidos',
                'details' => $validator->errors()
            ], 400);
        }

        try {
            // Validar solapes usando función SQL (cuando esté implementada)
            $data = $validator->validated();
            
            // TODO: Implementar validación con fn_ValidarSolapeContrato
            // $tieneSolape = DB::selectOne('SELECT dbo.fn_ValidarSolapeContrato(?, ?, ?, NULL) AS TieneSolape', [
            //     $data['IDEmpleado'], $data['FechaInicio'], $data['FechaFin']
            // ]);
            
            // if ($tieneSolape && $tieneSolape->TieneSolape) {
            //     return response()->json([
            //         'success' => false,
            //         'code' => -3,
            //         'error' => 'El contrato solapa con un contrato existente'
            //     ], 409);
            // }

            $data['UsuarioCreacion'] = auth()->user()->name ?? 'API_USER';
            
            $contrato = Contrato::create($data);

            return response()->json([
                'success' => true,
                'code' => 0,
                'data' => $contrato->load(['empleado', 'departamento', 'cargo']),
                'message' => 'Contrato creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'code' => -99,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $contrato = Contrato::with(['empleado', 'departamento', 'cargo', 'gestionSalarios'])
                           ->find($id);

        if (!$contrato) {
            return response()->json([
                'success' => false,
                'code' => -2,
                'error' => 'Contrato no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => $contrato,
            'message' => 'Contrato obtenido exitosamente'
        ]);
    }

    public function validarSolape(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDEmpleado' => ['required', 'integer'],
            'FechaInicio' => ['required', 'date'],
            'FechaFin' => ['nullable', 'date', 'after:FechaInicio'],
            'IDContratoExcluir' => ['nullable', 'integer']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => -1,
                'error' => 'Parámetros inválidos',
                'details' => $validator->errors()
            ], 400);
        }

        // Validación básica mientras se implementa la función SQL
        $data = $validator->validated();
        $fechaFin = $data['FechaFin'] ?? '2099-12-31';

        $solapes = Contrato::where('IDEmpleado', $data['IDEmpleado'])
                          ->where('Estado', 'Activo')
                          ->when($data['IDContratoExcluir'] ?? null, function($q, $excluir) {
                              $q->where('IDContrato', '!=', $excluir);
                          })
                          ->where(function($q) use ($data, $fechaFin) {
                              $q->whereBetween('FechaInicio', [$data['FechaInicio'], $fechaFin])
                                ->orWhereBetween(DB::raw('ISNULL(FechaFin, \'2099-12-31\')'), [$data['FechaInicio'], $fechaFin])
                                ->orWhere(function($subQ) use ($data, $fechaFin) {
                                    $subQ->where('FechaInicio', '<=', $data['FechaInicio'])
                                         ->where(DB::raw('ISNULL(FechaFin, \'2099-12-31\')'), '>=', $fechaFin);
                                });
                          })
                          ->exists();

        return response()->json([
            'success' => true,
            'code' => 0,
            'data' => [
                'tiene_solape' => $solapes,
                'mensaje' => $solapes ? 'Existe solape con contrato activo' : 'Sin solapes detectados'
            ]
        ]);
    }
}