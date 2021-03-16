<?php

namespace App\Http\Requests;

use App\Http\Enums\TurnVariantEnum;
use App\Models\Game;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TurnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return null !== $this->getGame()->findPlayerByName($this->request->get('nickname'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'turn'     => ['required', 'numeric', Rule::in(array_values(TurnVariantEnum::getConstants())),],
            'nickname' => ['required', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['messages' => $validator->messages()], Response::HTTP_BAD_REQUEST)
        );
    }

    public function wantsJson(): bool
    {
        return true;
    }

    private function getGame(): Game
    {
        return $this->game;
    }
}
