<?php

namespace App\Http\Controllers\API;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Str;

class BaseController extends Controller
{
    /**
     * BaseController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->baseName = str_replace('Controller', '', class_basename(static::class));
        $this->name = strtolower($this->baseName);
        $this->names = Str::plural($this->name);
    }

    /**
     * Dynamically retrieve class field.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
            case 'model':
                $class = 'App\\Models\\' . $this->baseName;
                break;
            case 'requestValidation':
                $class = 'App\\Http\\Requests\\' . $this->baseName . 'RequestValidation';
                break;
            case 'repository':
                $class = 'App\\Repositories\\Eloquent\\' . $this->baseName . 'Repository';
                break;
        }

        return app($class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $query = $this->model->select('*');
        $items = $query->paginate(request('perPage'));

        return $this->response($items->all());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->response($this->model->findOrFail($id));
    }

    /**
     * Bind request data and save model
     *
     * @param Model|null $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function save($model = null, $data = null)
    {
        $model = $model ?? $this->model;
        $attributes = $data ?? $this->request->only($model->getFillable());

        try {
            $model
                ->fill($attributes)
                ->save();
            $id =
                $model->id ??
                $model
                    ->query()
                    ->getConnection()
                    ->getPdo()
                    ->lastInsertId();
            $message = $this->baseName . ' #' . $id . ' saved successfully.';
            $code = 200;
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $code = 500;
        }

        $model->setAttribute('message', $message);

        return $this->response($model->getAttributes(), $code);
    }

    /**
     * Store new record.
     *
     * @return mixed
     */
    public function store()
    {
        return $this->save();
    }

    /**
     * Update existing record.
     *
     * @param  Model $model
     * @return mixed
     */
    public function update($model)
    {
        return $this->save($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->model->find($id)->delete();
            $message = $this->baseName . ' #' . $id . ' deleted successfully.';
            $code = 200;
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $code = 500;
        }

        return $this->response($message, $code);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function onUnauthorized()
    {
        return $this->response('You must login first.', 403);
    }

    /**
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data, $code = 200)
    {
        $success = $code == 200;
        $data =
            is_object($data) ?
                json_decode(json_encode($data), true) :
                (
                is_string($data) ?
                    ['message' => $data] :
                    $data
                );

        if (!isset($data['message'])) {
            $data['message'] = '';
        }

        $message = $data['message'];
        unset($data['message']);

        $response =
            [
                'success' => $success,
                'message' => $message,
                'data' => $data
            ];

        return response()->json($response, $code);
    }
}