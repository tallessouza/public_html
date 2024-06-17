<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserOpenai;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WordController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/words/remaining",
     *      operationId="getRemainingWords",
     *      tags={"Words"},
     *      summary="Get remaining words for the user identified by phone",
     *      description="Returns the number of remaining words for the user identified by phone",
     *      @OA\Parameter(
     *          name="phone",
     *          in="query",
     *          required=true,
     *          description="Phone number of the user",
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Remaining words retrieved successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="remaining_words", type="integer", example=1000),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *      ),
     * )
     */
    public function getRemainingWords(Request $request)
    {
        // Obter a chave de autorização do cabeçalho da requisição
        $authorizationHeader = $request->header('Authorization');

        // Comparar a chave de autorização com a variável de ambiente API_KEY
        if ($authorizationHeader !== env('API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('phone', '+'.$request->phone)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['remaining_words' => $user->remaining_words], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/words/decrease",
     *      operationId="decreaseWords",
     *      tags={"Words"},
     *      summary="Decrease words for the user identified by phone",
     *      description="Decreases the number of words for the user identified by phone",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"phone", "text"},
     *              @OA\Property(property="phone", type="string", example="+1234567890"),
     *              @OA\Property(property="text", type="string", example="Este é um exemplo de texto para contagem de palavras."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Words decreased successfully",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     * )
     */
    public function decreaseWords(Request $request)
    {
        // Obter a chave de autorização do cabeçalho da requisição
        $authorizationHeader = $request->header('Authorization');

        // Comparar a chave de autorização com a variável de ambiente API_KEY
        if ($authorizationHeader !== env('API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('phone', '+'.$request->phone)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $wordCount = countWords($request->text);

        // // Salvar o registro da mensagem
        // $entry = new UserOpenai();
        // $entry->title = 'Whatsapp Lendario';
        // $entry->slug = Str::random(20).Str::slug($user->fullName()).'-workbook';
        // $entry->user_id = $user->id;
        // $entry->input = $request->text;
        // $entry->response = $request->text;
        // $entry->output = $request->text;
        // $entry->hash = Str::random(256);
        // $entry->credits = $wordCount;
        // $entry->words = $wordCount;

        if ($user->remaining_words != -1) {
            userCreditDecreaseForWord($user, $wordCount);
        }
        // $entry->save();


        return response()->json(['message' => 'Words decreased successfully'], 200);
    }
}
