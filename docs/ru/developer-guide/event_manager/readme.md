# Event Manager #
При помощи Event Manager предоствляется возможность объявлять событие и сшушать это событие слушателем или слушателями в модулях.
### Как использовать? ###
```
$this->getContainer()->get('eventManager')->triggered($target, $action, $data); 
```
* $target - Условное название слушателя. То есть если $target='Foo', то будут активны слушатели в модулях которые размещены в `application/Espo/Modules/{MODULE_NAME}/Listeners/Foo.php`
* $action - метод который будет вызван в слушателе
* $data - данные которые будут переданы на слушатель
* метод EventManager::triggered() - всегда возврщает массив $data обратно, но массив $data может быть изменен слушателями.

## Пример: ##
```
$this->getContainer()->get('eventManager')->triggered('Foo', 'afterUpdate', ['name' => 'test']);
```
Для этого примера будут активны все слушатели Foo и в этих слушателях будет вызван метод afterUpdate. При этом на метод будет передан массив данных `['name' => 'test']`

## Спецификация: ##
* $data - массив
* Загрузка слушателей происходит в порядке загрузки модулей. При этом разные модули могут слушать одно и то же событие.
* В слушателе присутствует Container, на при условии если он унаследован от `Treo\Listeners\AbstractListener`.
* Слушатель может возвращать только модифицированный массив $data и далее следующим слушателям будет передан массив $data модифицированный предыдущим.