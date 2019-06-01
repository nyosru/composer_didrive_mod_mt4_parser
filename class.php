<?php

namespace Didrive\Mod;

if (!defined('IN_NYOS_PROJECT'))
    throw new Exception('Что то пошло не так, обратитесь к администратору', 666);

class Mt4 {

    public static function addNewData( $db, array $data, $clear_old = true ) {
        
        $db -> exec('DROP TABLE `my_forex` ;');

//        $ff = $db->prepare('DELETE FROM `my_forex` WHERE `folder` = :folder ;');
//        $ff->execute(array(':folder' => $folder));

        $db->exec('CREATE TABLE IF NOT EXISTS my_forex (
                `id_sdelka` INTEGER (20)    NOT NULL,
                `dt`        DATETIME,
                `dt2`       DATE,
                `type`      VARCHAR (8),
                `size`      DECIMAL (3, 2),
                `item`      VARCHAR (15),
                `sl`        DECIMAL (15, 6),
                `tp`        DECIMAL (16, 6),
                `dt_close`  DATETIME,
                `dt_close2` DATE,
                `price`     DECIMAL (16, 6),
                `comis`     DECIMAL (10, 2),
                `tax`       DECIMAL (10, 2),
                `swap`      DECIMAL (10, 2),
                `prof`      DECIMAL (10, 2),
                `folder`    VARCHAR (50)    NOT NULL
            ); ');

    \f\db\sql_insert_mnogo($db, 'my_forex', $in );
        
        
    }
    
    /**
     * парсинг стейтмента от герчика
     * @param string $link
     * @return type
     * @throws Exception
     */
    public static function parse_mt4_statement(string $link) {

        if (!file_exists($link))
            throw new Exception('Не найден файл данных');

        $html = \voku\helper\HtmlDomParser::file_get_html($link);

        $elements1 = $html->find('tr');

        $bb = [];

        foreach ($elements1 as $element1) {

            $b = [];

            $element2 = $element1->find('td');

            foreach ($element2 as $element) {
                $b[] = $element->plaintext;
            }

            if (isset($b[0]) && is_numeric($b[0])) {
                $bb[] = $b;
            }
        }

        $html->clear(); // подчищаем за собой
        unset($html);

        return $bb;
    }

    /**
     * получаем данные по операциям
     * @param type $db 
     * PDO
     * @param string $folder
     * @return array
     * items - операции сгруппированные по дням
     * mont - операции сгруппированные по месяцам
     */
    public static function getInfo($db, string $folder) {

        $ff = $db->prepare('SELECT dt2, sum(`prof`)  `sum_prof` , count(`id`) `colvo_items` FROM `my_forex` WHERE `folder` = :folder GROUP BY `dt2` ORDER BY `dt2` DESC ;');
        $ff->execute(array(':folder' => $folder));

        $ff1 = $db->prepare('SELECT strftime(\'%m\',dt2) `mont`, strftime(\'%Y\',dt2) `year` , sum(`prof`) `sum_prof` , count(`id`) `colvo_items` FROM `my_forex` WHERE `folder` = :folder GROUP BY strftime(\'%Y\',`dt2`), strftime(\'%m\',`dt2`) ;');
        $ff1->execute(array(':folder' => $folder));

        return array(
            'items' => $ff->fetchAll()
            ,
            'mont' => $ff1->fetchAll()
        );
        
    }

}
