## Api doc

Run php -S 0.0.0.0:8000 -t public

- [[GET] http://0.0.0.0:8000/api/game/opened](http://0.0.0.0:8000/api/game/opened) - Список открытых игр
- [[GET] http://0.0.0.0:8000/api/game/finished](http://0.0.0.0:8000/api/game/opened) - Список завершенных игр
- [[POST] http://0.0.0.0:8000/api/game/create](http://0.0.0.0:8000/api/game/create) - Создание игры (без параметров)
- [[POST]http://0.0.0.0:8000/api/{game}/enter]() - Вход в игру
  <br> game (int) - id игры
  <br> form-data:
  <br> nickname (string)
- [[POST]http://0.0.0.0:8000/api/game/{game}/turn]() - Выбор варианта (камень - 1, ножницы - 2, бумага - 3)
  <br> game (int) - id игры
  <br> form-data:
  <br> nickname (string)
  <br> turn (int) [1, 2, 3]
- [[GET]http://0.0.0.0:8000/api/game/{id}/info]() - Инфо об игре

Не углублялся в детали. Все не по феншую. Инфо игры доступно всего и всем. Тесты так же не делал

Просчет результата происходит в момент выбора варианта последнего игрока. Стадии лобби нет.
