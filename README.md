# 勤怠管理アプリ
## プロジェクトの目的
学習を目的とした、勤怠の管理を行うためのアプリです。
## 主要機能
- 一般ユーザーの機能
  - 会員登録、ログイン、ログアウト（メール認証はMailHogを使用）
  - 勤怠打刻
  - 勤怠一覧の確認
  - 勤怠詳細の確認
  - 勤怠の修正を申請
  - 勤怠修正申請一覧の確認
  - 修正申請詳細の確認
- 管理者の機能
  - ログイン、ログアウト
  - 日次勤怠の確認
  - 勤怠詳細の確認
  - 勤怠の修正
  - スタッフ一覧の確認
  - スタッフごとの勤怠の確認、CSV出力
  - 勤怠修正申請一覧の確認
  - 修正申請詳細の確認、承認
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
- 会員登録：http://localhost/register
- 管理者ログイン：http://localhost/admin/login
- 打刻画面：http://localhost/attendance
- mailhog：http://localhost:8025/
- phpMyAdmin：http://localhost:8080/
## ログイン情報
- 一般ユーザー
  - Email: user@example.com
  - Password: password123
- 管理者
  - Email: admin@example.com
  - Password: password123
## テーブル設計

![スクリーンショット (15)](https://github.com/user-attachments/assets/c060dd9c-1dac-40eb-95e9-fcf598c8c8d0)
![スクリーンショット (16)](https://github.com/user-attachments/assets/726d6d18-df60-4d6e-a66e-a91af0aa275a)

## ER図

![スクリーンショット (14)](https://github.com/user-attachments/assets/a55460f8-ee08-4945-95d9-4e13d8397578)

## 使用技術(実行環境)
- PHP8.3.0
- Laravel8.83.29
- MySQL8.0.26
- nginx1.21.1
- Docker
