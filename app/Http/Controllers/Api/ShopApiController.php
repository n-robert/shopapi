<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Str;

class ShopApiController extends Controller
{
    /**
     * ShopApiController constructor.
     *
     */
    public function __construct()
    {
        $this->baseName = str_replace(
            search: 'Controller',
            replace: '',
            subject: class_basename(static::class)
        );
        $this->name = strtolower($this->baseName);
        $this->names = Str::plural(value: $this->name);
    }

    /**
     * Dynamically retrieve class field.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
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

        return app(abstract: $class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $query = $this->model->select('*');
        $items = $query->paginate(request(key: 'perPage'));

        return $this->response(payload: $items->all());
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return $this->response(payload: $this->model->findOrNew((int)$id));
    }

    /**
     * Bind request data and save model
     *
     * @param Model|null $model
     * @param null $data
     * @return JsonResponse
     */
    public function save(Request $request, Model $model = null, $data = null): JsonResponse
    {
        $model = $model ?? $this->model;
        $attributes = $data ?? $request->only($model->getFillable());
        $message = '';
        $errors = '';

        try {
            $model->fill($attributes)->save();
            $id = $model->id ??
                  $model
                      ->query()
                      ->getConnection()
                      ->getPdo()
                      ->lastInsertId();
            $message = $this->baseName . ' #' . $id . ' saved successfully.';
            $status = 200;
        } catch (\Exception $exception) {
            $message = 'Save failed.';
            $errors = $exception->getMessage();
            $status = 500;
        }

        $model->setAttribute('message', $message);
        $model->setAttribute('errors', $errors);

        return $this->response(payload: $model->getAttributes(), status: $status);
    }

    /**
     * Store new record.
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return $this->save(request: $request);
    }

    /**
     * Update existing record.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $model = $this->model->find((int)$id);

        return $this->save(request: $request, model: $model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->model->find((int)$id)->delete();
            $message = $this->baseName . ' #' . $id . ' deleted successfully.';
            $code = 200;
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $code = 500;
        }

        return $this->response(payload: $message, status: $code);
    }

    /**
     * @return JsonResponse
     */
    public function onUnauthorized(): JsonResponse
    {
        return $this->response(payload: 'You must login first.', status: 403);
    }

    /**
     * @param $payload
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public function response($payload,
                             int $status = 200,
                             array $headers = [],
                             int $options = JSON_NUMERIC_CHECK): JsonResponse
    {
        $payload = is_string($payload) ? ['message' => $payload] : json_decode(json_encode($payload), true);;
        $payload['message'] = $payload['message'] ?? '';
        $payload['errors'] = $payload['errors'] ?? '';

        $success = ($status == 200);
        $message = $payload['message'];
        unset($payload['message']);
        $errors = $payload['errors'];
        unset($payload['errors']);

        $data = [
            'success' => $success,
            'message' => $message,
            'errors'  => $errors,
            'payload' => $payload,
        ];

        return response()->json($data, $status, $headers, $options);
    }
}
