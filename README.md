# 勤怠管理アプリ
## プロジェクトの目的
- 学習を目的とした、勤怠の管理を行うためのアプリです。
## 主要機能
- 
- 
- 
- 
  - 
  - 
  - 
  - 
  - 
## 環境構築
### Dockerビルド
1. git clone git@github.com:ssk-akn/fleamarket_simulation.git
2. DockerDesktopアプリを立ち上げる
3. docker-compose up -d --build
### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
```
php artisan key:generate
```
6. マイグレーションの実行
```
php artisan migrate
```
7. シーディングの実行
```
php artisan db:seedphp artisan storage:link
```
## 開発環境
- 商品一覧画面：http://localhost/
- 会員登録：http://localhost/register
- mailhog：http://localhost:8025/
- phpMyAdmin：http://localhost:8080/
## ログイン情報
- 管理者
  - Email: admin@example.com
  - Password: password123
- 一般ユーザー
  - Email: user@example.com
  - Password: password123
## テーブル設計

![スクリーンショット (7)](https://github.com/user-attachments/assets/15296a9c-0bfa-443f-aa17-66122f118971)
![スクリーンショット (8)](https://github.com/user-attachments/assets/4eea7c36-02f3-4113-a2ed-48f50a5b45c5)

## ER図

![スクリーンショット (9)](https://github.com/user-attachments/assets/89b6412d-3eea-46ff-83a6-98431a26ba66)


## 使用技術(実行環境)
- PHP8.3.0
- Laravel8.83.29
- MySQL8.0.26
- nginx1.21.1
- Docker
