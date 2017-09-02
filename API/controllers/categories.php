<?php
class Categories{
    private $params = [];
    private $data   = [];
    private $db     = NULL;
    function __construct($params, &$data, &$db){
        $this->params = $params;
        $this->data   = &$data;
        $this->db     = &$db;
    }
    function getCategories(){
        $this->data['arr']    = $this->db->query("SELECT * FROM `categories` WHERE `uid` = ? ORDER BY `type` ASC, `title` ASC", [$this->params['uid']]);
        $this->data['status'] = 'success';
    }
    function getCategory(){
        $this->data['arr']    = $this->db->query("SELECT * FROM `categories` WHERE `id` = ? AND `uid` = ?", [$this->params['id'], $this->params['uid']]);
        $this->data['arr']    = $this->data['arr'][0];
        $this->data['status'] = 'success';
    }
    function editCategory(){
        if (!$this->params['title'] || !$this->params['type']){
            $this->data['status'] = 'error';
            $this->data['msg']    = 'Помилка! Значення полів "Назва" та "Тип" не може бути пустим!';
        }
        else{
            if ($this->params['id']){     // edit category
                $this->db->query(
                    "UPDATE `categories` SET `title` = ?, `type` = ? WHERE `id` = ? AND `uid` = ?",
                    [$this->params['title'], $this->params['type'], $this->params['id'], $this->params['uid']]
                );
                $this->data['msg'] = "Готово! Категорія успішно змінена.";
            }
            else{     // add category
                $id = $this->db->query(
                    "INSERT INTO `categories` (`uid`, `title`, `type`) VALUES(?, ?, ?)",
                    [$this->params['uid'], $this->params['title'], $this->params['type']], NULL, true
                );
                $this->data['msg'] = "Готово! Категорія успішно додана.";
            }
            $id                = $this->params['id'] ? $this->params['id'] : $id;
            $this->data['arr'] = [
                id    => $id,
                uid   => $this->params['uid'],
                title => $this->params['title'],
                type  => $this->params['type']
            ];
            $this->data['status'] = 'success';
        }
    }
    function delCategory(){
        $count = $this->db->query("SELECT COUNT(*) AS `count` FROM `actions` WHERE `category_id` = ?", [$this->params['id']]);
        $count = $count[0]['count'];
        if ($count > 0){
            $this->data['status'] = 'error';
            $this->data['msg']    = "Помилка! Категорія використовується у $count транзакції(ях), спочатку треба видалити транзакції!";
        }
        else{
            $this->db->query("DELETE FROM `categories` WHERE `id` = ? AND `uid` = ?", [$this->params['id'], $this->params['uid']]);
            $this->data['status'] = 'success';
            $this->data['msg']    = "Готово! Категорія успішно видалена.";
        }
    }
}
?>
