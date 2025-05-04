=== Nip Finder ===
Contributors: asmtechpl
Donate link: https://code-press.pl/
Tags: comments, spam
Requires at least: 6.2
Tested up to: 6.8
Stable tag: 1.3.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wtyczka automatycznie pobiera dane firmowe z GUS po numerze NIP podczas zakupu, przyspieszając proces składania zamówień dla firm.

== Description ==

# Opis Wtyczki

**Wtyczka Automatycznego Pobierania Danych Firmowych z GUS**

W dzisiejszym szybkim tempie zakupów online, szczególnie w sektorze B2B, kluczowe jest skrócenie czasu realizacji zamówienia oraz minimalizacja ryzyka błędów przy wprowadzaniu danych. Nasza wtyczka została stworzona z myślą o firmach, które chcą usprawnić proces składania zamówień, automatyzując pobieranie niezbędnych informacji z Głównego Urzędu Statystycznego (GUS) na podstawie numeru NIP.

## Jak to działa?

Podczas procesu składania zamówienia w Twoim sklepie internetowym, klient będący przedstawicielem firmy wpisuje numer NIP. Wtyczka automatycznie łączy się z bazą danych GUS, pobierając szczegółowe dane firmowe, takie jak:

- **Nazwa firmy**
- **Adres siedziby**
- **Status podatkowy**
- **Numer REGON**
- **I inne istotne informacje**

Dzięki temu dane te zostają uzupełnione w formularzu zakupowym bez konieczności ręcznego wprowadzania przez klienta. Efektem jest przyspieszenie procesu realizacji zamówień, zmniejszenie liczby błędów oraz poprawa doświadczenia użytkownika.

Korzyści dla Twojego sklepu

- **Oszczędność czasu:** Automatyczne pobieranie danych firmowych eliminuje potrzebę ręcznego wpisywania informacji, co znacząco skraca czas finalizacji zamówienia.
- **Dokładność danych:** Integracja z bazą GUS zapewnia, że pobrane informacje są aktualne i precyzyjne, co zmniejsza ryzyko błędów i pomyłek.
- **Lepsze doświadczenie klienta:** Uproszczony proces zamawiania sprawia, że zakupy stają się bardziej intuicyjne i przyjazne, co może prowadzić do wzrostu satysfakcji i lojalności klientów.
- **Wsparcie dla firm:** Wtyczka jest idealnym rozwiązaniem dla firm, które często dokonują zakupów hurtowych lub mają rozbudowane systemy zakupowe, umożliwiając im szybkie i bezproblemowe składanie zamówień.

Wtyczka automatycznie pobiera dane firmowe z GUS na podstawie numeru NIP, co pozwala na błyskawiczne uzupełnienie formularzy zamówień i usprawnienie procesu zakupowego. Dzięki intuicyjnej konfiguracji, elastyczności oraz wysokiemu poziomowi bezpieczeństwa, stanowi idealne narzędzie dla firm pragnących zwiększyć efektywność swoich procesów zakupowych i zminimalizować ryzyko błędów.


== Installation ==


Instalacja oraz konfiguracja wtyczki jest prosta i intuicyjna:

1. **Instalacja:** Wystarczy pobrać wtyczkę z repozytorium, zainstalować ją w systemie CMS oraz aktywować.
2. **Konfiguracja:** W panelu administracyjnym znajdziesz dedykowaną sekcję, w której możesz ustawić parametry połączenia z bazą GUS, klucze API oraz inne opcje personalizacji.
3. **Personalizacja Formularza:** Dzięki elastycznemu kreatorowi formularzy możesz dostosować układ pól na stronie zamówienia, tak aby odpowiadały indywidualnym potrzebom Twojego biznesu.

== Frequently Asked Questions ==

= Czy wtyczka obsługuje numery NIP zagranicznych firm? =
Nie, wtyczka jest przeznaczona do pobierania danych z polskiej bazy GUS i obsługuje wyłącznie numery NIP polskich przedsiębiorstw.

= Jak często aktualizowane są dane w bazie GUS? =
GUS aktualizuje swoje dane regularnie, jednak częstotliwość aktualizacji może różnić się w zależności od rodzaju informacji. Wtyczka pobiera dane w czasie rzeczywistym, więc zawsze otrzymujesz najświeższe dostępne informacje.

= Czy mogę ręcznie edytować dane pobrane z GUS? =
Tak, po automatycznym pobraniu danych z GUS masz możliwość ich ręcznej edycji przed finalizacją zamówienia.

= Czy wtyczka jest płatna? =
Podstawowa wersja wtyczki jest dostępna za darmo. Istnieje jednak możliwość zakupu wersji premium z dodatkowymi funkcjonalnościami. Szczegóły znajdziesz na stronie wtyczki.

= Jak skontaktować się z pomocą techniczną w przypadku problemów? =
W przypadku pytań lub problemów związanych z działaniem wtyczki, prosimy o kontakt poprzez formularz wsparcia dostępny na naszej stronie internetowej lub bezpośrednio z poziomu panelu administracyjnego WordPressa w sekcji ustawień wtyczki.

== Screenshots ==



== Changelog ==


== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

== Usługi zewnętrzne ==

Ta wtyczka łączy się z zewnętrznym API, aby pobierać dane firmowe z GUS na podstawie numeru NIP podanego przez użytkownika podczas składania zamówienia.

- **Opis usługi:** Wtyczka automatycznie pobiera aktualne dane firmowe, takie jak nazwa firmy, adres siedziby, status podatkowy, numer REGON oraz inne istotne informacje, dzięki integracji z serwisem GUS.
- **Przesyłane dane:** Podczas procesu składania zamówienia wysyłany jest numer NIP użytkownika do API dostępnego pod adresem `https://api.gapc.pl`. W przypadku braku zgody lub dostępności danych, stosowany jest wcześniej zdefiniowany numer domyślny.
- **Warunki korzystania:** Korzystając z tej wtyczki, akceptujesz przesyłanie wskazanych danych do serwisu zewnętrznego. Aby zapoznać się z regulaminami usługi, odwiedź [Regulamin usługi](https://nip-finder.pl/regulamin/) oraz [Politykę prywatności](https://nip-finder.pl/polityka-prywatnosci/).
*Uwaga:* Informacja o korzystaniu z usług zewnętrznych została podana, aby zapewnić pełną przejrzystość procesu oraz zgodność z obowiązującymi przepisami o ochronie danych osobowych.

