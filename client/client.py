#!/usr/bin/env python3
"""Консольний клієнт для JSON API каталогу настільних ігор."""
import argparse
import getpass
import os
import re
import sys

import requests

BASE_URL = os.environ.get("API_URL", "http://127.0.0.1:8000")


def get_json(path, params=None):
    try:
        resp = requests.get(f"{BASE_URL}{path}", params=params, timeout=10)
        resp.raise_for_status()
        return resp.json()
    except requests.exceptions.ConnectionError:
        sys.exit(f"Не вдалося підключитися до {BASE_URL}. Запущено symfony serve?")
    except requests.exceptions.HTTPError as e:
        if e.response.status_code == 404:
            sys.exit("Не знайдено.")
        sys.exit(f"Помилка сервера: {e}")


def print_games(games):
    if not games:
        print("Нічого не знайдено.")
        return
    print(f"{'ID':<4}{'Назва':<24}{'Категорія':<16}{'Ціна':>8}  {'Гравці':<7}{'Наявн.'}")
    for g in games:
        players = f"{g['minPlayers']}-{g['maxPlayers']}"
        stock = "так" if g["inStock"] else "ні"
        print(f"{g['id']:<4}{g['title']:<24}{g['category']['name']:<16}"
              f"{g['price']:>8.0f}  {players:<7}{stock}")


def cmd_list(args):
    params = {
        "category": args.category,
        "maxPrice": args.max_price,
        "players": args.players,
        "q": args.query,
    }
    params = {k: v for k, v in params.items() if v is not None}
    print_games(get_json("/api/games", params))


def cmd_show(args):
    g = get_json(f"/api/games/{args.id}")
    print(f"{g['title']}  (категорія: {g['category']['name']})")
    print(f"Ціна: {g['price']:.0f} грн")
    print(f"Гравці: {g['minPlayers']}-{g['maxPlayers']}, партія ~{g['playTimeMinutes']} хв, {g['minAge']}+")
    print(f"В наявності: {'так' if g['inStock'] else 'ні'}")
    if g["description"]:
        print(g["description"])


def cmd_categories(args):
    for c in get_json("/api/categories"):
        print(f"{c['id']:<4}{c['name']:<18}{c['description'] or ''}")


def admin_login():
    """Вхід через форму Symfony з CSRF-токеном. Повертає сесію з cookie."""
    email = os.environ.get("ADMIN_EMAIL") or input("Email адміна: ")
    password = os.environ.get("ADMIN_PASSWORD") or getpass.getpass("Пароль: ")

    s = requests.Session()
    try:
        page = s.get(f"{BASE_URL}/login", timeout=10)
    except requests.exceptions.ConnectionError:
        sys.exit(f"Не вдалося підключитися до {BASE_URL}. Запущено symfony serve?")

    m = re.search(r'name="_csrf_token"[^>]*?value="([^"]+)"', page.text)
    if not m:
        sys.exit("Не знайдено CSRF-токен на /login")

    # Браузер сам додає ці заголовки, а Symfony перевіряє їх для CSRF
    headers = {
        "Origin": BASE_URL,
        "Referer": f"{BASE_URL}/login",
    }
    resp = s.post(f"{BASE_URL}/login", data={
        "_username": email,
        "_password": password,
        "_csrf_token": m.group(1),
    }, headers=headers, timeout=10)

    if resp.url.rstrip("/").endswith("/login"):
        if "CSRF" in resp.text:
            sys.exit("Сервер відхилив CSRF-токен.")
        sys.exit("Невірний логін або пароль.")
    return s


def cmd_admin_check(args):
    s = admin_login()
    resp = s.get(f"{BASE_URL}/admin", timeout=10)
    print(f"GET /admin -> {resp.status_code}")
    print("Підсумковий URL:", resp.url)
    if "/login" in resp.url:
        sys.exit("Адмінка не пустила: сесія не авторизована.")
    print("Вхід в адмінку успішний.")


def main():
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8")

    parser = argparse.ArgumentParser(description="Клієнт каталогу настільних ігор")
    sub = parser.add_subparsers(dest="command", required=True)

    p = sub.add_parser("list", help="список ігор")
    p.add_argument("--category", type=int)
    p.add_argument("--max-price", type=float)
    p.add_argument("--players", type=int)
    p.add_argument("--query", "-q")
    p.set_defaults(func=cmd_list)

    p = sub.add_parser("show", help="одна гра за id")
    p.add_argument("id", type=int)
    p.set_defaults(func=cmd_show)

    p = sub.add_parser("categories", help="список категорій")
    p.set_defaults(func=cmd_categories)

    p = sub.add_parser("admin-check", help="увійти в адмінку через сесію")
    p.set_defaults(func=cmd_admin_check)

    args = parser.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()