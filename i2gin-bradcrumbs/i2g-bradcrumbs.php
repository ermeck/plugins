<?php
/**
 * Plugin Name: Хлебные крошки в title
 * Description: Добавляем полный путь в title страниц. Для корректной работы плагина, тег title шаблона header.php должен выглядеть так: &lt;title&gt;&lt;?php wp_title(); ?&gt;&lt;/title&gt;
 * Plugin URI: http://ermeck.com
 * Author: i2gun
 * Author URI: http://ermeck.com
 */


// КОММЕНТАРИИ ДВОЙНЫМ СЛЕШЕМ - МОИ ПОЯСНЕНИЯ САМОМУ СЕБЕ
// КОММЕНТАРИИ ЗВЕЗДОЧКАМИ - ПОЯСНЕНИЯ АВТОРА



// это второй хук с функцией 'wp_title', первый в файле functions.php поэтому,
// ..чтобы он выполнился позже - задаем ему приоритет 20


add_filter( 'wp_title', 'i2g_title', 20 );  

function i2g_title ($title) {
    // обнуляем заголовок
    $title = null;
    // разделитель
    $sep = ' - ';
    // получаем название сайта
    $site = get_bloginfo( 'name' );

    /**
     * ГЛАВНАЯ СТРАНИЦА
     */
    // is_home() функция, true - если на главной странице лента последних записей
    // is_front_page() - true - если на главной странице - статичная страница
    if ( is_home() || is_front_page() ) {
        // В тайтле - массив, чтобы проще было оперировать
        $title = array($site);
        $title[] = 'test';
    }

    /**
     * ПОСТОЯННАЯ СТРАНИЦА
     */
    elseif ( is_page() ) {
        $title = array( get_the_title(), $site, );
    }

    /**
     * МЕТКА
     */
    // архив включает себя метки и еще много чего, засим метки нужно ставить ДО архива
    elseif ( is_tag() ) {
        // второй параметр false говорит, что мы не выводим, а возвращаем
        $title = array( single_tag_title( 'Метка: ', false ), $site );
    }

    /**
     * ПОИСК
     */
    elseif ( is_search() ) {
        $title = array( 'Результаты поиска по запросу: ', get_search_query() );
    }

    /**
     * 404
     */
    elseif ( is_404() ) {
        $title = array('Страница не найдена');
    }

    /**
     * КАТЕГОРИЯ
     */
    elseif ( is_category() ) {
        // ПОЛУЧАЕМ ID КАТЕГОРИИ
        $cat_id = get_query_var( 'cat' );
        // получаем данные категории по id ($cat - объект)
        $cat = get_category($cat_id);
        // ЕСЛИ ЕСТЬ РОДИТЕЛЬСКАЯ
        if ($cat->parent) {
            // получаем строку родительских категорий через разделитель
            // обрезаем ненужное с помощью rtrim, здесь ненужное - $sep
            $categories = rtrim(get_category_parents( $cat_id, false, $sep), $sep);
            // обращаем строку в массив
            $categories = explode($sep, $categories);
            // В наш массив $title получаем перевернутый массив
            $title = array_reverse($categories);
            // добавляем в массив название сайта
            $title[] = $site;
        } else {
            // ЕСЛИ НЕТ
            // формируем массив - название категории, название сайта
            $title = array($cat->name, $site);
        }
    }

    /**
     * ЗАПИСЬ
     */
    elseif ( is_single() ) {
        // массив данных о категориях
        $category = get_the_category();
        // ID категории
        $cat_id = $category[0]->cat_ID;
        // цепочку категорий получаем как в в предыдущем куске кода для категорий
        $categories = rtrim(get_category_parents( $cat_id, false, $sep), $sep);
        $categories = explode($sep, $categories);
        // только добавляем название записи
        $categories[] = get_the_title();
        $title = array_reverse($categories);
        $title[] = $site;
    }

    /**
     * АРХИВ
     */
    elseif ( is_archive() ) {
        $title = array( 'Архив за: ' . get_the_time( "F Y" ), $site );
    }

    // крамсаем заголовок на кусочки и превращаем в строку с разделителем
    $title = implode( $sep, $title );
    return $title;
}