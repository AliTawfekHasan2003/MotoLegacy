<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="API Motolegacy", version="1.0")
 *
 *  @OA\Server(
 *      url="https://KidziBack.squaretech.tech/motolegacy/api",
 *  )
 *  @OA\Server(
 *      url="http://127.0.0.1:8000/motolegacy/api",
 *  )
 *
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 *
 * @OA\Schema(
 *     schema="SseNotification",
 *     description="JSON payload of SSE event named notification. For EventSource use ?token=SANCTUM_TOKEN (do not send Bearer in the query).",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"car_created","car_updated","message_received","car_approval_status_updated","purchase_request_created","rental_request_created","purchase_request_status_updated","rental_request_status_updated"}
 *     ),
 *     @OA\Property(property="title", type="string", nullable=true),
 *     @OA\Property(property="body", type="string", nullable=true),
 *     @OA\Property(property="data", type="object", description="Includes rejection_reason when type is car_approval_status_updated, purchase_request_status_updated, or rental_request_status_updated"),
 *     @OA\Property(property="sent_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Car",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"available","hidden"}),
 *     @OA\Property(property="is_sold", type="boolean", description="true when hidden with an accepted purchase request"),
 *     @OA\Property(property="is_rented", type="boolean", description="true when hidden with an accepted rental request"),
 *     @OA\Property(property="approval_status", type="string", enum={"pending","approved","rejected"}),
 *     @OA\Property(property="rejection_reason", type="string", nullable=true, description="سبب رفض الأدمن للسيارة. يظهر للبائع مع إشعار car_approval_status_updated")
 * )
 *
 * @OA\Schema(
 *     schema="PurchaseRequest",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"pending","accepted","rejected"}),
 *     @OA\Property(property="rejection_reason", type="string", nullable=true, description="سبب رفض البائع لطلب الشراء. يظهر للمشتري مع إشعار purchase_request_status_updated")
 * )
 *
 * @OA\Schema(
 *     schema="RentalRequest",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"pending","accepted","rejected"}),
 *     @OA\Property(property="rejection_reason", type="string", nullable=true, description="سبب رفض البائع لطلب الإيجار. يظهر للمستأجر مع إشعار rental_request_status_updated")
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
