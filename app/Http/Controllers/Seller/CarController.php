<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
 /**
     * @OA\Post(
     *     path="/seller/cars",
     *     tags={"Seller - Cars"},
     *     summary="Add a new car for sale or rent",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category_id","type","name","year","brand","color","fuel_type","transmission","doors","seats"},
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="type", type="string", enum={"sale","rent"}),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="year", type="integer"),
     *                 @OA\Property(property="brand", type="string"),
     *                 @OA\Property(property="color", type="string"),
     *                 @OA\Property(property="fuel_type", type="string"),
     *                 @OA\Property(property="transmission", type="string"),
     *                 @OA\Property(property="doors", type="integer"),
     *                 @OA\Property(property="seats", type="integer"),
     *                 @OA\Property(property="purchase_price", type="number"),
     *                 @OA\Property(property="rental_price_per_day", type="number"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="previous_owners_count", type="integer"),
     *                 @OA\Property(property="registration_country", type="string"),
     *                 @OA\Property(property="engine_year", type="integer"),
     *                 @OA\Property(property="cylinders_count", type="integer"),
     *                 @OA\Property(property="drive_system", type="string"),
     *                 @OA\Property(property="plate_number", type="string"),
     *                 @OA\Property(property="fuel_consumption", type="string"),
     *                 @OA\Property(property="warranty", type="integer", enum={0,1}),
     *                 @OA\Property(property="warranty_duration", type="integer"),
     *                 @OA\Property(property="air_conditioning", type="integer", enum={0,1}),
     *                 @OA\Property(property="airbags", type="integer", enum={0,1}),
     *                 @OA\Property(property="rear_camera", type="integer", enum={0,1}),
     *                 @OA\Property(property="bluetooth", type="integer", enum={0,1}),
     *                 @OA\Property(property="sunroof", type="integer", enum={0,1}),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Car created"),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'          => 'required|exists:categories,id',
            'type'                 => 'required|in:sale,rent',
            'name'                 => 'required|string',
            'year'                 => 'required|integer',
            'brand'                => 'required|string',
            'color'                => 'required|string',
            'fuel_type'            => 'required|string',
            'transmission'         => 'required|string',
            'doors'                => 'required|integer',
            'seats'                => 'required|integer',
            'purchase_price'       => 'required_if:type,sale|nullable|numeric',
            'rental_price_per_day' => 'required_if:type,rent|nullable|numeric',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'previous_owners_count'=> 'nullable|integer',
            'registration_country' => 'nullable|string',
            'engine_year'          => 'nullable|integer',
            'cylinders_count'      => 'nullable|integer',
            'drive_system'         => 'nullable|string',
            'plate_number'         => 'nullable|string',
            'fuel_consumption'     => 'nullable|string',
            'warranty'             => 'nullable|in:0,1',
            'air_conditioning'     => 'nullable|in:0,1',
            'airbags'              => 'nullable|in:0,1',
            'rear_camera'          => 'nullable|in:0,1',
            'bluetooth'            => 'nullable|in:0,1',
            'sunroof'              => 'nullable|in:0,1',
            'warranty_duration'    => 'nullable|integer',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars/images', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }

        $car = Auth::user()->cars()->create($data);

        return new CarResource($car->load('owner', 'category'));
    }

    /**
     * @OA\Post(
     *     path="/seller/cars/{id}",
     *     tags={"Seller - Cars"},
     *     summary="Update car details (use _method=PUT in form-data)",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="type", type="string", enum={"sale","rent"}),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="year", type="integer"),
     *                 @OA\Property(property="brand", type="string"),
     *                 @OA\Property(property="color", type="string"),
     *                 @OA\Property(property="fuel_type", type="string"),
     *                 @OA\Property(property="transmission", type="string"),
     *                 @OA\Property(property="doors", type="integer"),
     *                 @OA\Property(property="seats", type="integer"),
     *                 @OA\Property(property="purchase_price", type="number"),
     *                 @OA\Property(property="rental_price_per_day", type="number"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="previous_owners_count", type="integer"),
     *                 @OA\Property(property="registration_country", type="string"),
     *                 @OA\Property(property="engine_year", type="integer"),
     *                 @OA\Property(property="cylinders_count", type="integer"),
     *                 @OA\Property(property="drive_system", type="string"),
     *                 @OA\Property(property="plate_number", type="string"),
     *                 @OA\Property(property="fuel_consumption", type="string"),
     *                 @OA\Property(property="warranty", type="integer", enum={0,1}),
     *                 @OA\Property(property="warranty_duration", type="integer"),
     *                 @OA\Property(property="air_conditioning", type="integer", enum={0,1}),
     *                 @OA\Property(property="airbags", type="integer", enum={0,1}),
     *                 @OA\Property(property="rear_camera", type="integer", enum={0,1}),
     *                 @OA\Property(property="bluetooth", type="integer", enum={0,1}),
     *                 @OA\Property(property="sunroof", type="integer", enum={0,1}),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Car updated"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function update(Request $request, Car $car)
    {
        $this->authorize('update', $car);

        $request->validate([
            'category_id'          => 'nullable|exists:categories,id',
            'type'                 => 'nullable|in:sale,rent',
            'name'                 => 'nullable|string',
            'year'                 => 'nullable|integer',
            'brand'                => 'nullable|string',
            'color'                => 'nullable|string',
            'fuel_type'            => 'nullable|string',
            'transmission'         => 'nullable|string',
            'doors'                => 'nullable|integer',
            'seats'                => 'nullable|integer',
            'purchase_price'       => 'nullable|numeric',
            'rental_price_per_day' => 'nullable|numeric',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'previous_owners_count'=> 'nullable|integer',
            'registration_country' => 'nullable|string',
            'engine_year'          => 'nullable|integer',
            'cylinders_count'      => 'nullable|integer',
            'drive_system'         => 'nullable|string',
            'plate_number'         => 'nullable|string',
            'fuel_consumption'     => 'nullable|string',
            'warranty'             => 'nullable|in:0,1',
            'air_conditioning'     => 'nullable|in:0,1',
            'airbags'              => 'nullable|in:0,1',
            'rear_camera'          => 'nullable|in:0,1',
            'bluetooth'            => 'nullable|in:0,1',
            'sunroof'              => 'nullable|in:0,1',

            'warranty_duration'    => 'nullable|integer',
        ]);

        $data = $request->except(['image', '_method']);

        if ($request->hasFile('image')) {
            if ($car->image) {
                $oldPath = str_replace(Storage::disk('public')->url(''), '', $car->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('cars/images', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }

        $car->update($data);

        return new CarResource($car->load('owner', 'category'));
    }

    /**
     * @OA\Delete(
     *     path="/seller/cars/{id}",
     *     tags={"Seller - Cars"},
     *     summary="Delete a car",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Car deleted"),
     * )
     */
    public function destroy(Car $car)
    {
        $this->authorize('delete', $car);

        // Delete image from storage if exists
        if ($car->image) {
            $oldPath = str_replace(Storage::disk('public')->url(''), '', $car->image);
            Storage::disk('public')->delete($oldPath);
        }

        $car->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/seller/cars/{id}/toggle-visibility",
     *     tags={"Seller - Cars"},
     *     summary="Hide or show a car",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Visibility toggled"),
     * )
     */
    public function toggle_visibility(Car $car)
    {
        $this->authorize('update', $car);
        $car->status = $car->status === 'hidden' ? 'available' : 'hidden';
        $car->save();
        return new CarResource($car);
    }

    /**
     * @OA\Get(
     *     path="/seller/cars",
     *     tags={"Seller - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="List all visible cars with optional filters",
     *     @OA\Parameter(name="brand", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="type", in="query", @OA\Schema(type="string", enum={"sale","rent"})),
     *     @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="min_price", in="query", @OA\Schema(type="number")),
     *     @OA\Parameter(name="max_price", in="query", @OA\Schema(type="number")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function index(Request $request)
    {
        $user = to_user(Auth::user());

        $query = Car::where('user_id', $user->id)->with(['owner', 'category'])->where('status', '!=', 'hidden');

        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }
        if ($request->has('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('purchase_price', '>=', $request->min_price)
                  ->orWhere('rental_price_per_day', '>=', $request->min_price);
            });
        }
        if ($request->has('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('purchase_price', '<=', $request->max_price)
                  ->orWhere('rental_price_per_day', '<=', $request->max_price);
            });
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return CarResource::collection($query->latest()->paginate(10));
    }

    /**
     * @OA\Get(
     *     path="/seller/cars/{id}",
     *     tags={"Seller - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="Get car details",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Car $car)
    {
        return new CarResource($car->load('owner', 'category'));
    }
}
