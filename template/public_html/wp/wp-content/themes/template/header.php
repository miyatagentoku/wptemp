<!DOCTYPE html>
<html lang="ja">

    <head>

    <?php get_template_part('inc/controller/head'); ?>

    <?php wp_head(); ?>

    </head>

    <body>

        <?php get_template_part('inc/controller/header'); // 共通ヘッダー設定読み込み ?>

        <?php get_template_part('inc/controller/nav'); // 共通グローバルナビ設定読み込み ?>

        <div class="c-main" role="main">

            <?php get_template_part('inc/controller/breadcrumb'); // 共通パンくず設定読み込み ?>