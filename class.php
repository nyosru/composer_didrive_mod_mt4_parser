<?php

namespace Nyos\Mod;

if (!defined('IN_NYOS_PROJECT'))
    throw new Exception('Что то пошло не так, обратитесь к администратору', 666);

class Mt4 {

    public static function addNewData($db, string $folder, array $data, $clear_old = true) {

        // echo '<Br/>'.__FILE__.' ('.__LINE__.')';
        // $db->exec('DROP TABLE `my_forex` ;');

        try {

            if ($clear_old === true) {
                $ff = $db->prepare('DELETE FROM `my_forex` WHERE `folder` = :folder ;');
                $ff->execute(array(':folder' => $folder));
            }
            
        } catch (\PDOException $ex) {

            if (strpos($ex->getMessage(), 'no such table') !== false) {

                $db->exec('CREATE TABLE my_forex (
                    `folder`    VARCHAR (50)    NOT NULL,
                    `Ticket` INTEGER (20)    NOT NULL,
                    `OpenTime`        DATETIME,
                    `Type`      VARCHAR (8),
                    `Size`      DECIMAL (5, 2),
                    `Item`      VARCHAR (15),
                    `Price`      DECIMAL (8, 5),
                    `SL`      DECIMAL (5, 2),
                    `TP`      DECIMAL (5, 2),
                    `CloseTime`        DATETIME,
                    `Commission`      DECIMAL (5, 2),
                    `Taxes`      DECIMAL (5, 2),
                    `Swap`      DECIMAL (5, 2),
                    `Profit`      DECIMAL (5, 2)
                    ); ');
            }
            
        }


        \f\db\sql_insert_mnogo($db, 'my_forex', $data, array('folder' => $folder));
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

        $a = ['-', '/', ' '];
        $a1 = '';

        foreach ($elements1 as $element1) {

            $b = [];
            $element2 = $element1->find('td');

            foreach ($element2 as $element) {
                if (!isset($hh)) {
                    $b[] = str_replace($a, $a1, $element->plaintext);
                } else {
                    $b[] = $element->plaintext;
                }
            }

            if (!isset($hh) && isset($b[0]) && $b[0] == 'Ticket') {
                // \f\pa($b);
                $hh = 1;
                $bb1 = $b;
            } elseif (isset($b[0]) && is_numeric($b[0])) {

                foreach ($b as $k => $v) {
                    if ($bb1[$k] == 'OpenTime') {
                        $b1[$bb1[$k]] = str_replace('.', '-', $v);
                    } else {
                        $b1[$bb1[$k]] = $v;
                    }
                }

                $bb[] = $b1;
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

        $ff = $db->prepare('SELECT 
                OpenTime, 
                sum(`Profit`)  `sum_prof` , 
                count(`Ticket`) `colvo_items` 
            FROM 
                `my_forex` 
            WHERE 
                `folder` = :folder 
            GROUP BY 
                strftime(\'%Y-%m-%d\',`OpenTime`)
            ORDER BY 
                `OpenTime` DESC 
            ;');
        $ff->execute(array(':folder' => $folder));

        $ff1 = $db->prepare('SELECT 
                strftime(\'%m\',`OpenTime`) `mont`, 
                strftime(\'%Y\',`OpenTime`) `year` , 
                sum(`Profit`) `sum_prof` , 
                count(`Ticket`) `colvo_items` 
            FROM 
                `my_forex` 
            WHERE 
                `folder` = :folder 
            GROUP BY 
                strftime(\'%Y\',`OpenTime`), 
                strftime(\'%m\',`OpenTime`) 
            ;');
        $ff1->execute(array(':folder' => $folder));

        return array(
            'items' => $ff->fetchAll()
            ,
            'mont' => $ff1->fetchAll()
        );

//        $ff1 = $db->prepare('SELECT * FROM `my_forex` LIMIT 1 ;');
//        //$ff1->execute(array(':folder' => $folder));
//        $ff1->execute();
//
//        return $ff1->fetchAll();
    }

}
