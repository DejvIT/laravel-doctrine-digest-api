# Background

- Potrebujeme vytvoriť REST API systém ktorý by distribuoval články od bloggerov ku odberateľom.
- Odberateľov môže byť niekoľko desiatok tisíc, blogerov niekoľko desiatok.
- Aktívny prístup do systému majú len bloggeri skrz login, odberatelia ho nemajú..
- Každy článok ma kategóriu, taktiež blogger prispieva článkami minimálne do jednej alebo viac kategorií. Blogger NEMÔŽE
  vytvoriť článok v inej kategorií než ku ktorej je priradený.
- Články sa distribuujú odberateľom 2x denne, po uzávierke ktorá je ráno o 11:00 a poobede o 17:00. Distribujú sa len
  články ktoré ešte neboli distribuované.
- Distribúcia prebieha formou emailu, kde sa zhrnú všetky články odovzdané pred uzávierkou do jedného emailu - nechceme
  spamovať odberateľov novým emailom pre každý nový článok.

# Requirements

1. Pripraviť si Doctrine entities, Doctrine repositories, DB seeders pre Blogger, Subscriber, Article, ArticleCategory
2. Pripraviť si autentifikáciu pre bloggerov, najlepšie pomocou Bearer JWT tokenov - ale keep it simple! :) 
3. Pripraviť si autorizáciu pre bloggerov
4. Pripraviť CRUD pre Articles (nedovoliť update/delete už distribuovaných článkov), GET pre ArticleCategory, GET
   pre subscribers - myslieť na potencionálnu filtráciu alebo aspon odprezentovat navrh
5. Pripraviť asyn funkcionalitu pre zhrnutie nových článkov do jedného emailu a jeho poslanie odberateľom po uzávierke -
   treba myslieť na performance

# Nice to have

1. Integration tests
2. ...

# Epilogue

1. Reálne netreba rozposielať emaily skrz nejakú SMTP službu, stačí to mocknuť.
2. Ako primárne kľúče používame UUID - je to už pripravené v kostre

# Hints

- project start (see for more info: https://laravel.com/docs/11.x#docker-installation-using-sail)

  ```bash
  $ ./vendor/bin/sail up
  ```
  
- generate migration in doctrine

  ```bash
  $ ./vendor/bin/sail artisan doctrine:migrations:diff
  ```

- apply migration in doctrine

  ```bash
  $ ./vendor/bin/sail artisan doctrine:migrations:migrate
  ```
