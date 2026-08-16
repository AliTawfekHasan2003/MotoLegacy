<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\RentalRequest;
use App\Http\Resources\CarResource;
use App\Services\SseNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
 /**
     * @OA\Post(
     *     path="/seller/cars",
     *     tags={"Seller - Cars"},
     *     summary="Add a new car for sale or rent (notifies admins via SSE type car_created)",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category_id","type","name","year","brand","color","fuel_type","transmission","doors","seats","condition"},
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
     *                 @OA\Property(property="condition", type="number", format="float", minimum=1, maximum=5, description="Car condition rating from 1 to 5 (e.g. 4.5)"),
     *                 @OA\Property(property="purchase_price", type="number"),
     *                 @OA\Property(property="rental_price_per_day", type="number"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="gallery[]", type="array", @OA\Items(type="string", format="binary")),
     *                 @OA\Property(property="ownership_document", type="string", format="binary", description="Car ownership / registration document"),
     *                 @OA\Property(property="insurance_document", type="string", format="binary", description="Insurance document"),
     *                 @OA\Property(property="inspection_document", type="string", format="binary", description="Technical inspection document"),
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
            'condition'            => 'required|numeric|min:1|max:5',
            'purchase_price'       => 'required_if:type,sale|nullable|numeric',
            'rental_price_per_day' => 'required_if:type,rent|nullable|numeric',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery'              => 'nullable|array',
            'gallery.*'            => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'ownership_document'   => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'insurance_document'   => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'inspection_document'  => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
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

        $data = $request->except([
            'image',
            'gallery',
            'ownership_document',
            'insurance_document',
            'inspection_document',
            'approval_status',
            'rejection_reason',
            'user_id',
            'is_sold',
            'is_rented',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars/images', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }

        $data['gallery'] = $this->storeGalleryFiles($request->file('gallery', []));
        $data = array_merge($data, $this->storeDocumentFiles($request));

        $car = Auth::user()->cars()->create($data);
        $car->load('owner', 'category');

        app(SseNotifier::class)->notifyAdminsOfPendingCar($car);

        return new CarResource($car);
    }
    

    /**
     * @OA\Post(
     *     path="/seller/cars/{id}",
     *     tags={"Seller - Cars"},
     *     summary="Update car details (use _method=PUT in form-data). If the car was rejected, approval_status becomes pending, rejection_reason is cleared, and admins get SSE type car_updated. Approved/pending cars keep their approval_status.",
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
     *                 @OA\Property(property="condition", type="number", format="float", minimum=1, maximum=5, description="Car condition rating from 1 to 5 (e.g. 4.5)"),
     *                 @OA\Property(property="purchase_price", type="number"),
     *                 @OA\Property(property="rental_price_per_day", type="number"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="gallery[]", type="array", @OA\Items(type="string", format="binary")),
     *                 @OA\Property(property="ownership_document", type="string", format="binary", description="Car ownership / registration document"),
     *                 @OA\Property(property="insurance_document", type="string", format="binary", description="Insurance document"),
     *                 @OA\Property(property="inspection_document", type="string", format="binary", description="Technical inspection document"),
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
            'condition'            => 'nullable|numeric|min:1|max:5',
            'purchase_price'       => 'nullable|numeric',
            'rental_price_per_day' => 'nullable|numeric',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'ownership_document'   => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'insurance_document'   => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'inspection_document'  => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
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

        $data = $request->except([
            'image',
            'gallery',
            'ownership_document',
            'insurance_document',
            'inspection_document',
            '_method',
            'approval_status',
            'rejection_reason',
            'user_id',
            'status',
            'is_sold',
            'is_rented',
        ]);

        if ($request->hasFile('image')) {
            if ($car->image) {
                $this->deleteStoredFile($car->image);
            }

            $path = $request->file('image')->store('cars/images', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }

        $data = array_merge($data, $this->storeDocumentFiles($request, $car));

        $wasRejected = $car->approval_status === 'rejected';
        if ($wasRejected) {
            $data['approval_status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $car->update($data);
        $car->load('owner', 'category');

        if ($wasRejected) {
            app(SseNotifier::class)->notifyAdminsOfUpdatedCar($car);
        }

        return new CarResource($car);
    }

    /**
     * @OA\Post(
     *     path="/seller/cars/{id}/gallery",
     *     tags={"Seller - Cars"},
     *     summary="Add images to car gallery",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"gallery[]"},
     *                 @OA\Property(property="gallery[]", type="array", @OA\Items(type="string", format="binary")),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Gallery images added"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function addGalleryImages(Request $request, Car $car)
    {
        $this->authorize('update', $car);

        $request->validate([
            'gallery'   => 'required|array|min:1',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $existing = $car->gallery ?? [];
        $newUrls = $this->storeGalleryFiles($request->file('gallery', []));
        $car->update(['gallery' => array_values(array_merge($existing, $newUrls))]);

        return new CarResource($car->load('owner', 'category'));
    }

    /**
     * @OA\Delete(
     *     path="/seller/cars/{id}/gallery",
     *     tags={"Seller - Cars"},
     *     summary="Remove an image from car gallery by URL",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(property="url", type="string", example="http://localhost/storage/cars/gallery/example.jpg")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Gallery image removed"),
     *     @OA\Response(response=404, description="Image not found in gallery"),
     *     @OA\Response(response=403, description="Unauthorized"),
     * )
     */
    public function removeGalleryImage(Request $request, Car $car)
    {
        $this->authorize('update', $car);

        $request->validate([
            'url' => 'required|string',
        ]);

        $gallery = $car->gallery ?? [];
        $url = $request->url;

        if (!in_array($url, $gallery, true)) {
            return response()->json(['message' => 'Image not found in gallery'], 404);
        }

        $this->deleteStoredFile($url);
        $car->update([
            'gallery' => array_values(array_filter($gallery, fn ($item) => $item !== $url)),
        ]);

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

        if ($car->image) {
            $this->deleteStoredFile($car->image);
        }

        foreach ($car->gallery ?? [] as $url) {
            $this->deleteStoredFile($url);
        }

        foreach (['ownership_document', 'insurance_document', 'inspection_document'] as $field) {
            if ($car->{$field}) {
                $this->deleteStoredFile($car->{$field});
            }
        }

        $car->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/seller/cars/{id}/toggle-visibility",
     *     tags={"Seller - Cars"},
     *     summary="Hide or show a car",
     *     description="POST /seller/cars/{id}/toggle-visibility. Body field: request_approve_id (integer, optional, nullable) — purchase or rental request id. Without it: car becomes hidden, is_sold=false, is_rented=false. With it: that request is accepted, others rejected; is_sold=true if car type is sale, is_rented=true if type is rent. Hidden cars disappear from user and admin lists; seller still sees them. Showing again sets status=available and is_sold=is_rented=false. Requests are not restored.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="request_approve_id",
     *                     type="integer",
     *                     nullable=true,
     *                     description="Optional for sale and rent. If sent: accept this request, set is_sold or is_rented to true, reject the rest. If omitted (null): only hide the car."
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Visibility toggled"),
     * )
     */
    public function toggle_visibility(Request $request, Car $car)
    {
        $this->authorize('update', $car);

        $request->validate([
            'request_approve_id' => 'nullable|integer',
        ]);

        $willHide = $car->status !== 'hidden';
        $approveId = $request->input('request_approve_id');

        if ($willHide) {
            $this->rejectOtherRequestsOnHide($car, $approveId);
            $car->status = 'hidden';
            $car->is_sold = (bool) $approveId && $car->type === 'sale';
            $car->is_rented = (bool) $approveId && $car->type === 'rent';
        } else {
            $car->status = 'available';
            $car->is_sold = false;
            $car->is_rented = false;
        }

        $car->save();

        return new CarResource($car->load('owner', 'category'));
    }

    private function rejectOtherRequestsOnHide(Car $car, $approveId = null): void
    {
        $approveId = $approveId ? (int) $approveId : null;
        $reason = 'The car is no longer available';
        $notifier = app(SseNotifier::class);

        if ($car->type === 'sale') {
            if ($approveId) {
                $approved = PurchaseRequest::where('car_id', $car->id)->where('id', $approveId)->first();
                if (!$approved) {
                    abort(422, 'Invalid request_approve_id for this car');
                }

                if ($approved->status !== 'accepted') {
                    $approved->update([
                        'status' => 'accepted',
                        'rejection_reason' => null,
                    ]);

                    $notifier->send(
                        $approved->user_id,
                        'purchase_request_status_updated',
                        [
                            'request_id' => $approved->id,
                            'car_id' => $car->id,
                            'status' => 'accepted',
                            'rejection_reason' => null,
                        ],
                        'Purchase request accepted',
                        'Your purchase request was accepted'
                    );
                }
            }

            $toReject = PurchaseRequest::where('car_id', $car->id)
                ->where('status', '!=', 'rejected')
                ->when($approveId, fn ($q) => $q->where('id', '!=', $approveId))
                ->get();

            foreach ($toReject as $purchaseRequest) {
                $purchaseRequest->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                ]);

                $notifier->send(
                    $purchaseRequest->user_id,
                    'purchase_request_status_updated',
                    [
                        'request_id' => $purchaseRequest->id,
                        'car_id' => $car->id,
                        'status' => 'rejected',
                        'rejection_reason' => $reason,
                    ],
                    'Purchase request rejected',
                    $reason
                );
            }

            return;
        }

        if ($approveId) {
            $approved = RentalRequest::where('car_id', $car->id)->where('id', $approveId)->first();
            if (!$approved) {
                abort(422, 'Invalid request_approve_id for this car');
            }

            if ($approved->status !== 'accepted') {
                $approved->update([
                    'status' => 'accepted',
                    'rejection_reason' => null,
                ]);

                $notifier->send(
                    $approved->user_id,
                    'rental_request_status_updated',
                    [
                        'request_id' => $approved->id,
                        'car_id' => $car->id,
                        'status' => 'accepted',
                        'rejection_reason' => null,
                    ],
                    'Rental request accepted',
                    'Your rental request was accepted'
                );
            }
        }

        $toReject = RentalRequest::where('car_id', $car->id)
            ->where('status', '!=', 'rejected')
            ->when($approveId, fn ($q) => $q->where('id', '!=', $approveId))
            ->get();

        foreach ($toReject as $rentalRequest) {
            $rentalRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            $notifier->send(
                $rentalRequest->user_id,
                'rental_request_status_updated',
                [
                    'request_id' => $rentalRequest->id,
                    'car_id' => $car->id,
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                ],
                'Rental request rejected',
                $reason
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/seller/cars",
     *     tags={"Seller - Cars"},
     *     security={{"bearer_token":{}}},
     *     summary="List seller cars including hidden, sold, and rented",
     *     description="Seller sees all own cars. Filter sold with is_sold=true, rented with is_rented=true, temporarily hidden with status=hidden&is_sold=false&is_rented=false.",
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="brand", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="type", in="query", @OA\Schema(type="string", enum={"sale","rent"})),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"available","hidden"})),
     *     @OA\Parameter(name="is_sold", in="query", @OA\Schema(type="boolean"), description="Filter sold cars"),
     *     @OA\Parameter(name="is_rented", in="query", @OA\Schema(type="boolean"), description="Filter rented cars"),
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

        $query = Car::where('user_id', $user->id)->with(['owner', 'category']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('is_sold')) {
            $query->where('is_sold', filter_var($request->is_sold, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->has('is_rented')) {
            $query->where('is_rented', filter_var($request->is_rented, FILTER_VALIDATE_BOOLEAN));
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
     *     summary="Get seller car details including hidden/sold/rented cars",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     * )
     */
    public function show(Car $car)
    {
        $this->authorize('update', $car);

        return new CarResource($car->load('owner', 'category'));
    }

    /**
     * @param  array<\Illuminate\Http\UploadedFile>|null  $files
     * @return array<int, string>
     */
    private function storeGalleryFiles(?array $files): array
    {
        $urls = [];

        foreach ($files ?? [] as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('cars/gallery', 'public');
            $urls[] = Storage::disk('public')->url($path);
        }

        return $urls;
    }

    /**
     * @return array<string, string>
     */
    private function storeDocumentFiles(Request $request, ?Car $car = null): array
    {
        $data = [];

        foreach (['ownership_document', 'insurance_document', 'inspection_document'] as $field) {
            if (!$request->hasFile($field)) {
                continue;
            }

            if ($car && $car->{$field}) {
                $this->deleteStoredFile($car->{$field});
            }

            $path = $request->file($field)->store('cars/documents', 'public');
            $data[$field] = Storage::disk('public')->url($path);
        }

        return $data;
    }

    private function deleteStoredFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = str_replace(Storage::disk('public')->url(''), '', $url);
        Storage::disk('public')->delete($path);
    }
}
