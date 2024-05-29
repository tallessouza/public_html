<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Nette\Utils\Image;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserSupport;
use App\Models\UserSupportMessage;


class UserSupportApiController extends Controller {


    /**
     * Gets all support requests
     *
     * @OA\Get(
     *      path="/api/support/",
     *      operationId="supportRequests",
     *      tags={"Support"},
     *      summary="Gets all support requests",
     *      description="Gets all support requests",
     *      security={{ "passport": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
    */
    public function supportRequests(Request $request) {

        $perPage = $request->input('per_page', 10);

        $user = Auth::user();
        if ($user->type == 'admin')
            // admin will check from oldest hence asc and for mobile display only waiting for answer requests
            $items = UserSupport::where([['status', '>=', "Wai"]])->orderBy('updated_at', 'asc')->paginate($perPage);
        else
            // user will check from latest hence desc
            $items = UserSupport::where([['user_id', '=', $user->id]])->orderBy('updated_at', 'desc')->paginate($perPage);

        return response()->json($items, 200);
    }


    /**
     * Gets all messages of a support request
     *
     * @OA\Get(
     *      path="/api/support/ticket/{ticket_id}",
     *      operationId="ticket",
     *      tags={"Support"},
     *      summary="Gets all messages of a support request",
     *      description="Gets all messages of a support request. Use ticket ids like QZDNGSIFPH not integers.",
     *      security={{ "passport": {} }},
     *      @OA\Parameter(
     *          name="ticket_id",
     *          in="path",
     *          description="Ticket ID",
     *          required=true,
     *          @OA\Schema(type="string", example="QZDNGSIFPH"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ticket Not Found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function ticket(Request $request, String $ticket_id) {

        if($ticket_id == null) return response()->json(['error' => __('Ticket ID missing.')], 412);

        $ticket = UserSupport::where('ticket_id', $ticket_id)->firstOrFail();

        if(Auth::user()->type != 'admin' && $ticket->user_id != Auth::id()){
            return response()->json(['error' => __('Unauthorized request.')], 403);
        }

        $perPage = $request->input('per_page', 10);

        $messages = UserSupportMessage::where([["user_support_id", "=", $ticket->id]])->orderBy('updated_at', 'desc')->paginate($perPage);

        return response()->json($messages, 200);
    }


    /**
     * Gets latest message of a support request
     *
     * @OA\Get(
     *      path="/api/support/ticket/{ticket_id}/last-message",
     *      operationId="ticketLastMessage",
     *      tags={"Support"},
     *      summary="Gets latest message of a support request",
     *      description="Gets latest message of a support request. Use ticket ids like QZDNGSIFPH not integers.",
     *      security={{ "passport": {} }},
     *      @OA\Parameter(
     *          name="ticket_id",
     *          in="path",
     *          description="Ticket ID",
     *          required=true,
     *          @OA\Schema(type="string", example="QZDNGSIFPH"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ticket Not Found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function ticketLastMessage(Request $request, String $ticket_id) {

        if($ticket_id == null) return response()->json(['error' => __('Ticket ID missing.')], 412);

        $ticket = UserSupport::where('ticket_id', $ticket_id)->firstOrFail();

        if(Auth::user()->type != 'admin' && $ticket->user_id != Auth::id()){
            return response()->json(['error' => __('Unauthorized request.')], 403);
        }

        $messages = UserSupportMessage::where([["user_support_id", "=", $ticket->id]])->orderBy('created_at', 'desc')->first();

        return response()->json($messages, 200);
    }




    /**
     * Create new support request
     *
     * @OA\Post(
     *      path="/api/support/new-ticket",
     *      operationId="newTicket",
     *      tags={"Support"},
     *      summary="Create new support request",
     *      description="Create new support request",
     *      security={{ "passport": {} }},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request support ticket data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="priority",
     *                     description="Priority",
     *                     type="string",
     *                     enum={"Low", "Normal", "High", "Critical"}
     *                 ),
     *                 @OA\Property(
     *                     property="category",
     *                     description="Category",
     *                     type="string",
     *                     enum={"General Inquiry", "Technical Issue", "Improvement Idea", "Feedback", "Other"}
     *                 ),
     *                  @OA\Property(
     *                     property="subject",
     *                     description="Subject of the Message",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     description="Message to send",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function newTicket(Request $request) {

        if($request->priority == null) return response()->json(['error' => __('Priority missing.')], 412);
        if($request->category == null) return response()->json(['error' => __('Category missing.')], 412);
        if($request->subject == null) return response()->json(['error' => __('Subject missing.')], 412);
        if($request->message == null) return response()->json(['error' => __('Message missing.')], 412);

        $support = new UserSupport();
        $support->ticket_id = Str::upper(Str::random(10));
        $support->user_id = Auth::id();
        $support->priority = $request->priority;
        $support->category = $request->category;
        $support->subject = $request->subject;
        $support->save();

        $message = new UserSupportMessage();
        $message->user_support_id = $support->id;
        $message->message = $request->message;
        $message->save();
        createActivity(Auth::id(), 'Submitted a Ticket', $support->subject, null);

        return response()->json(['message' => 'Ticket submitted'], 200);
    }
    

    /**
     * Send message to support request
     *
     * @OA\Post(
     *      path="/api/support/send-message",
     *      operationId="sendMessage",
     *      tags={"Support"},
     *      summary="Send message to support request",
     *      description="Send message to support request",
     *      security={{ "passport": {} }},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request message data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="ticket_id",
     *                     description="Ticket ID",
     *                     type="string",
     *                     example="QZDNGSIFPH"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     description="Message to send",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function sendMessage(Request $request) {

        if($request->ticket_id == null) return response()->json(['error' => __('Ticket ID missing.')], 412);
        if($request->message == null) return response()->json(['error' => __('Message missing.')], 412);


        $user = Auth::user();
        $ticket = UserSupport::where('ticket_id', $request->ticket_id)->firstOrFail();
        if ($user->type == 'admin'){
            $ticket->status = 'Answered';
            $ticket->save();

            $message = new UserSupportMessage();
            $message->user_support_id = $ticket->id;
            $message->sender = 'admin';
            $message->message = $request->message;
            $message->save();
        }else{
            $ticket->status = 'Waiting for answer';
            $ticket->save();

            $message = new UserSupportMessage();
            $message->user_support_id = $ticket->id;
            $message->sender = 'user';
            $message->message = $request->message;
            $message->save();
            createActivity(Auth::id(), 'Support request waiting for your answer', null,  null);
        }

        return response()->json(['message' => 'Message sent'], 200);

    }



    /**
     * Gets information of the user which sent the request
     *
     * @OA\Get(
     *      path="/api/support/user/{ticket_id}",
     *      operationId="ticketUser",
     *      tags={"Support"},
     *      summary="Gets information of the user which sent the request",
     *      description="Gets information of the user which sent the request. Use ticket ids like QZDNGSIFPH not integers.",
     *      security={{ "passport": {} }},
     *      @OA\Parameter(
     *          name="ticket_id",
     *          in="path",
     *          description="Ticket ID",
     *          required=true,
     *          @OA\Schema(type="string", example="QZDNGSIFPH"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Ticket / User Not Found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Precondition Failed",
     *      ),
     * )
    */
    public function ticketUser(Request $request, String $ticket_id) {

        if($ticket_id == null) return response()->json(['error' => __('Ticket ID missing.')], 412);

        if(Auth::user()->type != 'admin'){
            return response()->json(['error' => __('Unauthorized request.')], 403);
        }

        $ticket = UserSupport::where('ticket_id', $ticket_id)->firstOrFail();

        $userId = $ticket->user_id;

        $userData = User::where([["id", "=", $userId]])->firstOrFail();

        return response()->json($userData, 200);
    }




}