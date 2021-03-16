<?php

namespace App\Http\Requests;

use App\Models\GameUser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class EnterGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /** @var GameUser $gameUserEntity */
        $gameUserEntity = App::make(GameUser::class);

        return [
            'nickname' => ['required', 'string', 'max:255', Rule::unique($gameUserEntity->getTable())->where(function ($query) {
                return $query->where('game_id', $this->getGameId());
            })],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['messages' => $validator->messages()], Response::HTTP_BAD_REQUEST)
        );
    }

    private function getGameId(): int
    {
        return $this->game->id;
    }
}
