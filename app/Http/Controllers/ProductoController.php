<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportsDownloadExport;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Collection as Collection;

use DB, Storage, Lang, App, Log, SendEmail, Response, Excel, TuProyecto, Auth;

use App\Models\Core\Producto,
    App\Models\Core\CategoriaProducto,
    App\Models\Core\EvaluacionCompra,
    App\Models\Core\CategoriaPadreProducto,
    App\Models\Core\Compra,
    App\Models\Core\Estado,
    App\Models\Seguridad\Usuario,
    App\Models\Core\AdjuntoProducto,
    App\Models\Core\Persona;

class ProductoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,marketplace');
        $this->middleware('ability:admin|super-admin|administrativo,create,marketplace', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,marketplace', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,marketplace', ['only' => ['destroy']]);
        $this->middleware('ability:admin|super-admin|administrativo,export,marketplace', ['only' => ['exportar']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            return Producto::getDataProducts( $request->all() );
        }

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive'), Estado::getState('review')], $filter = null);
        return view('marketplace.main', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $producto = Producto::getProduct($id);
        $averagePurchase = $producto->usuario->getAveragePurchase(Compra::getTypeUser('purchaser'));
        $entity = Usuario::find($producto->seg_codigo_usuario)->nombre_usuario;
        $adjuntoProducto = AdjuntoProducto::getAttached($producto->codigo_producto);
        $img_principal = ['direccion_adjunto' => $producto->imagen_producto];
        array_unshift($adjuntoProducto, $img_principal);
        return view('marketplace.detail', compact('producto', 'entity', 'averagePurchase', 'adjuntoProducto'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
			// Decrypt src-params
			list($codigo_producto) = TuProyecto::decryptSrcParams($id);
			if(!$codigo_producto || !is_numeric($codigo_producto)) {
				throw new \Exception("El código no es correcto {$codigo_producto}");
			}

			$product = Producto::find($codigo_producto);
			if(!$product instanceof Producto) {
				throw new \Exception("Producto no encontrado {$codigo_producto}");
            }

            $user = Usuario::find($product->codigo_usuario);
            if(!$user instanceof Usuario){
                throw new \Exception("Usuario no encontrado {$product->codigo_usuario}");
            }

            DB::beginTransaction();
            try {
				if($request->has('change_state')){
					switch($request->get('action')){
						case 'aprobar':
							$message = 'aprobado';
                            $codigo_estado = Estado::getState('active');
                            $product->sync_algolia = 1;
							break;
						case 'rechazar':
							$message = 'rechazado';
                            $codigo_estado = Estado::getState('pending');

                            // Send mail refused product
                            $data = [];
                            $data['_template'] = 'emails.marketplace.productrefused';
                            $data['_inst'] = session('institution')->codigo_institucion;
                            $data['_subject'] = 'Devolución producto';
                            $data['_to'] = $user->correo_electronico;
                            $data['_to_name'] = $user->nombre_usuario;
                            $data['_product'] = $product->nombre_producto;
                            $data['_comment'] = $request->comment_refused_product;
                            SendEmail::notification($data);
							break;
						case 'inactivar':
							$message = 'inactivado';
                            $codigo_estado = Estado::getState('inactive');
                            $product->sync_algolia = 3;
							break;
						case 'activar':
							$message = 'activado';
                            $codigo_estado = Estado::getState('active');
                            $product->sync_algolia = 1;
							break;
					}
				}

				$product->codigo_estado = $codigo_estado;
				$product->save();

                if($codigo_estado == Estado::getState('inactive')){
                    $product->sync_algolia = 3;
                    $product->save();
                    $product->desyncIndex();
                }

                // Commit Transaction
                DB::commit();
                if($product->sync_algolia === 1)
                {
                    $product->syncIndex();
                }

                return response()->json(['success' => true, 'message' => "El producto {$product->nombre_producto} se ha {$message} correctamente.", 'codigo_estado' => $product->codigo_estado]);
            }catch(\Exception $e){
                DB::rollback();
                Log::error(sprintf('%s:%s', 'ProductoController:update', $e->getMessage()));
                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
            }
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Export pdf the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportar(Request $request)
    {
        $user = Usuario::find(Auth::user()->codigo_usuario);
		if(!$user instanceof Usuario) {
			throw new \Exception("Error recuperando usuario en sesión.");
        }

        $query = Producto::query();
        $query->select(
            'producto.*',
            'nombre_estado',
            'nombre_categoria_producto',
            'nombre_cat_padre_producto',
            'nombre_clase_producto',
            'usuario.correo_electronico',
            'usuario.nombre_usuario',
            /*DB::raw("(usuario.nombre_usuario || ' '
                || COALESCE(persona.primer_apellido_persona, '')
                || ' ' || COALESCE(persona.segundo_apellido_persona, ''))
                as nombre_usuario"),*/
            'entity.nombre_usuario as nombre_entidad'
        );
        $query->join('seguridad.usuario as entity', 'producto.seg_codigo_usuario', '=', 'entity.codigo_usuario');
        $query->join('seguridad.usuario', 'producto.codigo_usuario', '=', 'usuario.codigo_usuario');
        $query->join('core.clase_producto', 'producto.codigo_clase_producto', '=', 'clase_producto.codigo_clase_producto');

        $query->leftJoin('core.categoria_producto_insti', function($join) {
            $join->on('producto.codigo_categoria_prod_insti', '=', 'categoria_producto_insti.codigo_categoria_prod_insti')
                ->where('categoria_producto_insti.codigo_institucion', '=', session('institution')->codigo_institucion)
                ->where('categoria_producto_insti.codigo_estado', '=', Estado::getState('active'));
        });

        $query->leftJoin('core.categoria_producto', 'categoria_producto_insti.codigo_categoria_producto', '=', 'categoria_producto.codigo_categoria_producto');
        $query->leftJoin('core.cat_padre_producto', 'cat_padre_producto.codigo_cat_padre_producto', '=', 'categoria_producto_insti.codigo_cat_padre_producto');
        $query->join('core.estado', 'producto.codigo_estado', '=', 'estado.codigo_estado');

       // $query->join('core.persona','producto.codigo_usuario','=','persona.codigo_usuario');

        if($request->has('name_product') && !empty($request->name_product)){
            $query->whereRaw("nombre_producto ilike '%".$request->name_product."%'");
        }

        if($request->has('seg_codigo_usuario') && !empty($request->seg_codigo_usuario)) {
            $query->where("producto.seg_codigo_usuario", $request->seg_codigo_usuario);
        }

		if($request->has('category') && !empty($request->category)) {
			$query->where('categoria_producto_insti.codigo_cat_padre_producto', $request->category);
		}

		if($request->has('subcategory') && !empty($request->subcategory)) {
			$query->where('categoria_producto_insti.codigo_categoria_prod_insti', $request->subcategory);
		}

        if($request->has('codigo_ciudad') && !empty($request->codigo_ciudad)) {
            $query->where('entity.codigo_ciudad', $request->codigo_ciudad);
        }

        if($request->has('codigo_estado') && !empty($request->codigo_estado)){
            $query->where('producto.codigo_estado', $request->codigo_estado);
        }

		$query->where('producto.codigo_institucion', '=', session('institution')->codigo_institucion);
        $query->orderBy('producto.codigo_producto', 'asc');
        $productos = $query->get();


        // Prepare header
        $header = [];
        $header[] = 'Número';
        $header[] = 'Proveedor';
        $header[] = 'Usuario';
        $header[] = 'Email';
        $header[] = 'Producto';
        $header[] = 'Categoría';
        $header[] = 'Subcategoría';
        $header[] = 'Clase';
        $header[] = 'Precio';
        $header[] = 'Estado';

        $item = 1;
        $data = collect([]);
        foreach ($productos as $producto)
        {
            $apellido = Persona::getLastName($producto->codigo_usuario);

            unset($record);
            $record[] = $item;
            $record[] = $producto->nombre_entidad;
            $record[] = $producto->nombre_usuario ." $apellido";
            $record[] = $producto->correo_electronico;
            $record[] = $producto->nombre_producto;
            $record[] = $producto->nombre_cat_padre_producto;
            $record[] = $producto->nombre_categoria_producto;
            $record[] = $producto->nombre_clase_producto;
            $record[] = $producto->precio_producto;
            $record[] = $producto->nombre_estado;
            $data[] = $record;

            $item++;
        }

        $filename = "Productos";

        activity()
			->performedOn($user)
			->withProperties([
				'attributes' => [
					'user' => $user->nombre_usuario,
					'user_id' => $user->codigo_usuario,
					'report' => $filename
				]
			])
		->log('activitylog.reports');

      return (new ReportsDownloadExport([$header], [$data]))->download('Productos.xlsx');
    }
}
