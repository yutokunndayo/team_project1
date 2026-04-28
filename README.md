# 災害情報管理システム

この project は 災害時の安否確認、社員情報管理、報告一覧表示をまとめた Web システムです。
PHP と MySQL を使って、ログイン後に管理ダッシュボードから各機能へ移動できる構成になっています。

## Project の目的

- 災害発生時に社員の安否をすばやく把握する
- 社員情報を一元管理する
- 安否報告を一覧・詳細で確認する
- 管理者が全体状況を把握しやすい画面を提供する

## 主な機能

- ログイン / ログアウト
- 社員登録 / 社員編集 / 社員一覧表示
- 安否報告の入力
- 安否報告一覧表示
- 安否報告詳細表示
- 安全 / 要対応の集計表示
- 安全一覧の確認画面

## 画面ごとの説明

### `login.php`

ログイン画面です。
メールアドレスとパスワードを入力してログインします。
ログイン成功後は `report.php` に移動します。

### `logout.php`

セッションを破棄してログアウトします。
ログイン状態を完全に解除して `login.php` に戻します。

### `index.php`

管理ダッシュボードです。
社員数、管理者数、安否報告数、安全報告数、要対応報告数を集計して表示します。
最新の社員一覧と最新の安否報告もここで確認できます。

### `register.php`

社員の新規登録と編集を行う画面です。
`id` がある場合は編集モードになります。
名前、メール、電話、部署、役職、入社日、生年月日、住所、管理者権限、パスワードを扱います。

### `register_lish.php`

社員一覧画面の本体です。
登録済み社員を一覧表示し、編集・削除ができます。

### `register_list.php`

`register_lish.php` を読み込むためのラッパーです。
実質的には社員一覧ページの入口です。

### `report.php`

社員が安否報告を入力する画面です。
ログイン中のユーザー情報を使って、社員番号・名前・部署を自動表示します。
安否状況は `安全` または `安全じゃない` を選び、コメントを送信します。

### `report_list.php`

安否報告一覧画面です。
最新報告の表示だけでなく、`status=safe` または `status=unsafe` で絞り込み表示できます。
`data=safe` でも安全一覧を表示できます。

### `report_detail.php`

安否報告の詳細画面です。
`emp_no` または `id` を指定して、1件の報告内容を表示します。
管理者以外でも一覧へ戻れるようになっています。

### `anquan.php`

社員の安全一覧を表示する画面です。
全報告を時系列で見ながら、安全 / 非安全を確認できます。

### `admin.php` / `admin_menu.php` / `reprot_del.php`

現時点では簡易ページまたは未使用の補助ファイルです。
メイン機能は上記のページで完結しています。

### `email.php`

現在は未実装の補助ページです。
パスワード再設定機能の入口として使う想定です。

## 使用技術

- PHP
- MySQL
- Bootstrap 5
- Bootstrap Icons
- JavaScript
- HTML / CSS

## データベース構成

### `register` テーブル

社員情報を保存します。

- `id`: 社員ID
- `name`: 名前
- `email`: メールアドレス
- `password`: パスワード
- `con_password`: 確認用パスワード
- `phone`: 電話番号
- `deployment`: 部署
- `position`: 役職
- `hiring_date`: 入社日
- `date_of_birth`: 生年月日
- `address`: 住所
- `is_admin`: 管理者フラグ
- `created_at`: 登録日時

### `report` テーブル

安否報告を保存します。

- `emp_no`: 社員番号
- `name`: 名前
- `deployment`: 部署
- `comment`: コメント
- `data`: 安否状態 (`安全` / `安全じゃない`)
- `created_at`: 登録日時

## 処理の流れ

1. ユーザーが `login.php` でログインする
2. セッションに `id`、`email`、`is_admin` を保存する
3. `report.php` で安否報告を入力する
4. `report_list.php` と `report_detail.php` で報告を確認する
5. 管理者は `index.php` から社員情報や全体状況を把握する
6. 必要に応じて `register.php` で社員情報を追加・更新する

## セットアップ方法

1. このフォルダを Web サーバーの公開ディレクトリに置く
2. MySQL で `災害` データベースを作成する
3. `______ (1).sql` をインポートする
4. `conn.php` の接続情報を環境に合わせて調整する
5. ブラウザで `login.php` を開く

## 接続設定

`conn.php` で DB 接続をしています。
ローカル環境では以下の値になっています。

- host: `localhost`
- user: `root`
- password: `root`
- database: `災害`

環境によってはここを変更してください。

## ファイル構成

- `login.php`: ログイン画面
- `logout.php`: ログアウト処理
- `index.php`: ダッシュボード
- `register.php`: 社員登録・編集
- `register_lish.php`: 社員一覧本体
- `register_list.php`: 社員一覧の入口
- `report.php`: 安否報告入力
- `report_list.php`: 安否報告一覧
- `report_detail.php`: 安否報告詳細
- `anquan.php`: 安全一覧
- `admin.php`: 簡易ページ
- `admin_menu.php`: 簡易ページ / 未使用
- `email.php`: パスワード再設定入口（未実装）
- `reprot_del.php`: 未使用ファイル
- `conn.php`: DB 接続
- `css/app.css`: 共通デザイン
- `login.css`: 旧ログインデザイン用 CSS（現在は未使用）
- `js/app.js`: 共通 JavaScript
- `______ (1).sql`: DB 定義と初期データ

## 注意点

- `register_lish.php` は名前に টাইपो があるままですが、`register_list.php` から読み込まれて使われています。
- `login.php` は現在 Bootstrap ベースのデザインに変更済みで、`login.css` は使っていません。
- `email.php` や `reprot_del.php` など、未実装または補助的なファイルがあります。

## 今後の改善案

- パスワードを平文ではなくハッシュ化して保存する
- 管理者専用ページと一般ユーザー用ページを分離する
- 安否報告の検索・絞り込み機能を強化する
- パスワード再設定機能を実装する
- 画面ごとの入力チェックをさらに厳密にする
