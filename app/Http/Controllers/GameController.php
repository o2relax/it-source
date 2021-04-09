<?php


namespace App\Http\Controllers;


use App\Http\Enums\TurnVariantEnum;
use App\Http\Requests\EnterGameRequest;
use App\Http\Requests\TurnRequest;
use App\Models\Game;
use App\Models\GameUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{

    private const WIN_VARIANTS = [
        TurnVariantEnum::ROCK     => TurnVariantEnum::SCISSORS,
        TurnVariantEnum::PAPER    => TurnVariantEnum::ROCK,
        TurnVariantEnum::SCISSORS => TurnVariantEnum::PAPER,
    ];

    private Game $gameModel;

    public function __construct(Game $gameModel)
    {
        $this->gameModel = $gameModel;
    }

    public function getListOpened(): JsonResponse
    {
        return response()->json($this->gameModel->findOpened());
    }

    public function getListFinished(): JsonResponse
    {
        return response()->json($this->gameModel->findFinished());
    }

    public function postCreate(): JsonResponse
    {
        $game = new Game;
        $game->save();

        return response()->json(['game_id' => $game->id, 'api' => route('game.enter', ['game' => $game->id])]);
    }

    public function postEnter(Game $game, EnterGameRequest $request): JsonResponse
    {
        if (!$request->validated()) {
            return response(Response::HTTP_BAD_REQUEST)->json([$request->validationData()]);
        }

        // можно решить с замыканием и вторым параметром на максимальное число висящих транзакций и кидать исключение
//        DB::transaction(static function() {
//
//        }, Game::MAX_PLAYER_COUNT);
        // иммитируем завершившийся запрос до начала транзакции
//        DB::table('games')->where('id', $game->id)->update(['player_count' => 3]);

        // к сожалению не нашел, как в ларавел заблокировать только одну строку
        DB::beginTransaction();

//        $actualGame = $game;

        /** @var Game $actualGame */
        $actualGame = Game::query()->find($game->id);

        if (null === $actualGame) {
            DB::rollBack();
            return response()->json(['messages' => ['error' => 'Game not found']], Response::HTTP_BAD_REQUEST);
        }

        if ($actualGame->player_count >= Game::MAX_PLAYER_COUNT) {
            DB::rollBack();
            return response()->json(['messages' => ['error' => 'Game room is full']], Response::HTTP_BAD_REQUEST);
        }

        $player = new GameUser;
        $player->nickname = $request->request->get('nickname');

        $actualGame->players()->save($player);
        $actualGame->player_count++;
        $actualGame->save();

        DB::commit();

        return response()->json(['api' => route('game.turn', ['game' => $actualGame->id])]);
    }

    public function postTurn(Game $game, TurnRequest $request): JsonResponse
    {
        if (null !== $game->finished_at) {
            return response()->json(['messages' => ['error' => 'Game was finished']], Response::HTTP_BAD_REQUEST);
        }

        if ($game->players()->count() !== Game::MAX_PLAYER_COUNT) {
            return response()->json(['messages' => ['error' => 'Game was not started']], Response::HTTP_BAD_REQUEST);
        }

        $gameUser = $game->findPlayerByName($request->request->get('nickname'));

        if ($gameUser) {
            if (null !== $gameUser->turn) {
                return response()->json(['messages' => ['error' => 'Already turned']], Response::HTTP_BAD_REQUEST);
            }
            $gameUser->turn = $request->request->getInt('turn');
            $gameUser->save();
        }

        if ($game->players()->get()->filter(static fn(GameUser $gameUser) => $gameUser->turn === null)->isEmpty()) {
            $this->calculateGameResult($game);
        }

        return response()->json(['api' => route('game.info', ['id' => $game->id])]);
    }

    public function getInfo(int $id): JsonResponse
    {
        $game = $this->gameModel::query()->with('players')->findOrFail($id);

        return response()->json($game);
    }

    private function calculateGameResult(Game $game): void
    {
        /** @var GameUser $player */
        foreach ($game->players()->get() as $player) {
            /** @var GameUser $opponent */
            foreach ($game->players()->get()->filter(static fn(GameUser $gameUser) => $gameUser->nickname !== $player->nickname) as $opponent) {
                if ($player->turn === $opponent->turn) {
                    continue;
                }

                if (self::WIN_VARIANTS[$player->turn] === $opponent->turn) {
                    $player->points++;
                } else {
                    $player->points--;
                }
            }
            $player->save();
        }

        $game->finished_at = new \DateTime;
        $game->save();
    }
}
