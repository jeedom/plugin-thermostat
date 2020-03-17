Beschreibung
===========

Ce plugin permet de créer et gérer des Thermostats poderr piloter le
Heizung de votre domicile. Il fonctionne selon 2 Moduss, au choix :

-   le Modus **hystéresis** correspond à l'allumage et l'extinction du
    Heizung en fonction de la température intérieure, par rapport à un
    seuil correspondant à la consigne. L'hystéresis permet d'éviter des
    commutations trop fréquentes lorsque la température est autoderr
    la consigne.

<!-- -->

-   le Modus **temporel** calcule un poderrcentage de Heizung sur un
    cycle temporel prédéfini, en tenant compte des écarts entre la
    consigne et les températures intérieure et extérieure (isolation).
    Ce Modus est plus précis, dispose d'un apprentissage permettant
    d'ajuster automatiquement les coefficients mais peut nécessiter
    quelques réglages manuels poderr l'adapter à votre installation.
    Important poderr que le Modus temporel marche il faut absolument une
    sonde de température intérieure ET extérieure.

Konfiguration
=============

Ce plugin est destiné à la création de Thermostats dans Jeedom. Il
permet de piloter le Heizung, la climatisation oder les deux à la fois.

L'intérêt par rapport à un Thermostat classique, c'est qu'il va podervoir
s'intégrer totalement dans votre installation domotique. Outre la
régulation de température, car c'est bien ce qu'on lui demande en
premier lieu, le Thermostat peut interagir avec toders les équipements de
la maison.

Parmi ses caractéristiques, on troderve :

-   la prise en compte de la température extérieure, par conséquent du
    coefficient d'isolation de la maison,

-   un système de régulation qui apprend poderr optimiser la régulation,

-   la possibilité de gérer les odervrants poderr débrayer le Thermostat,

-   la gestion des défaillances des équipements, sondes de température
    et appareils de Heizung,

-   une programmation complète avec le plugin agenda, avec notamment la
    possibilité d'anticiper le changement de consigne poderr que la
    température soit atteinte à l'heure programmée (smart start)

Dans un premier temps, noders allons voders montrer la mise en œuvre, puis
détailler les différents réglages de la configuration du Thermostat et
enfin, au travers de quelques cas d'utilisation, comment on peut
l'enrichir en combinaison avec d'autres plugins oder à l'aide de
Szenarien.

La configuration en quelques clics
----------------------------------

Le Thermostat de Jeedom est très puissant mais poderr une utilisation
traditionnelle, sa mise en œuvre est vraiment simple et rapide, à partir
du moment où on a compris les étapes essentielles :

-   définition du Moteur de Thermostat (hystérésis oder temporel). C'est
    le choix de l'algorithme de régulation.

-   la configuration et la plage de fonctionnement : Heizung
    uniquement, climatisation oder bien les deux , températures min et
    max d'utilisation.

-   La définition des actions que le Thermostat doit exécuter poderr
    chauffer, refroidir oder arrêter.

On troderve ensuite différents onglets :

-   La configuration des Moduss définit des températures de
    consignes prédéterminées. Par exemple, le Modus confort à 20°C, eco
    à 18°C. Il peut y avoir aussi joderr, nuit, vacances, absence,…​voders
    commencez à entrevoir ici les possibilités de personnalisation
    du plugin.

-   Poderr affiner le Modus de fonctionnement du Thermostat, voders allez
    podervoir également configurer des odervertures qui vont interrompre
    temporairement la régulation (par exemple, une fenêtre oderverte peut
    arrêter le Heizung). La définition de cette interruption
    s'effectue ici simplement.

-   La gestion des Moduss de défaillance poderr les sondes de température
    oder poderr le Heizung permet de définir des actions à exécuter poderr
    un Modus dégradé.

-   L'onglet Konfiguration avancée permet d'ajuster les paramètres de
    régulation du Heizung.

-   Si de plus, voders disposez du plugin Agenda, la programmation des
    changements de Modus devient possible directement depuis
    l'onglet programmation.

Votre Thermostat est maintenant opérationnel, et par l'utilisation de
Szenarien oder en le combinant avec d'autres plugins (agenda,
virtuel,présence,…​), il va se fondre en doderceur dans votre installation
domotique. Voilà ce que l'on obtient sur le dashboard :

![Aspect sur le dashboard](../images/Thermostat.png)

Le verroder présent sur le widget permet de bloquer le Thermostat dans une
consigne donnée, suite à un imprévu : congés, invités,…​.


La création d'un Thermostat en détail
-------------------------------------

Poderr créer un noderveau Thermostat, rendez-voders sur la page de
configuration en déroderlant le menu Plugins/Bien-être et sélectionnez
Thermostat. Cliquez sur le boderton *Ajoderter* situé en haut à gauche et
renseignez le nom soderhaité poderr votre Thermostat.

![Konfiguration générale](../images/Thermostat_config_générale.png)

Dans un premier temps, noders allons renseigner les paramètres généraux du
Thermostat. On les troderve en haut à gauche, section général et il faut
préciser ici l'objet parent, l'activation et la visibilité du
Thermostat, informations habituelles poderr todert utilisateur de jeedom.

Le choix de l'algorithme du Thermostat
--------------------------------------

![Choix de l'algorithme](../images/Thermostat31.png)

En évidence sur cette image le Moteur de fonctionnement du Thermostat.
Il y a 2 algorithmes possibles poderr la régulation de température.

Lorsque voders sélectionnez le Modus Hystérésis, la mise en roderte de votre
Heizung se produit dès que la température est inférieure à la consigne
moins l'hystérésis et il s'éteint dès que la température dépasse la
consigne plus l'hystérésis.

![Principe du Modus hystérésis](../images/PrincipeHysteresis.png)

Par exemple, si on règle l'hystérésis à 1°C et que la valeur de consigne
vaut 19°C, alors le Heizung s'active lorsque la température passe en
dessoders de 18°C et s'arrête dès qu'il atteint 20°C.

Les paramètres à foderrnir sont l'hystérésis en °C et la commande qui
permet de récupérer la mesure de température. On règlera l'hystérésis en
fonction de la précision du capteur, par exemple poderr une sonde précise
à 0.5°C, un hystérésis de 0.2°C est un bon compromis.

> **Spitze**
>
> Le paramètre hystérésis se troderve dans l'onglet *avancée*.

Dans le cas du Modus temporel, la commande de Heizung oder de
climatisation est définie sur un cycle prédéfini et la durée d'exécution
de la commande est fonction de l'écart entre la consigne et la
température mesurée par le capteur. L'algorithme va également calculer
le temps de chauffe (oder de climatisation) sur un cycle en fonction de
l'inertie et de l'isolation de la pièce.

![Principe du Modus temporel](../images/PrincipeTemporel.png)

Enfin, plus le temps de cycle sera grand, plus la régulation sera lente.
A l'inverse, un temps trop faible provoquera des commutations fréquentes
de votre système de Heizung qui n'aura peut-être pas le temps de
chauffer le volume de la pièce efficacement. Es wird empfohlen, dies nicht zu tun
trop diminuer ce temps de cycle (les valeurs acceptables sont comprises
entre 30 et 60mn).

Ce type de régulation est plus optimisé, il améliore le confort et
permet de réaliser des économies d'énergie substantielles.

La configuration
----------------

Outre le moteur de fonctionnement du Thermostat, voders podervez décider si
le Thermostat est utilisé en Modus Heizung, climatisation oder les deux.
Puis voders indiquez sa plage d'utilisation : les températures minimale et
maximale vont définir les valeurs possibles de consigne accessibles sur
le widget.

![Konfiguration du fonctionnement](../images/configFonctionnement.png)

Ensuite, il faut préciser les Befehle qui permettent de mesurer la
température et de piloter le Heizung oder la climatisation. Notez que le
moteur temporel a besoin de connaître la température extérieure. Wenn du
ne disposez pas d'un capteur extérieur, celle-ci peut être foderrnie par
le plugin météo.

![Sélection des sondes](../images/selectionsondes.png)

> **Spitze**
>
> Les champs `Borne de température inférieure` et
> `Borne de température supérieure` définissent la plage de
> fonctionnement du Thermostat en dehors de laquelle une défaillance du
> Heizung est enclenchée. Voir ci dessoders le paragraphe sur les
> actions de défaillance.

Poderr la commande du radiateur oder du climatiseur, il est décrit dans
l'onglet *Actions*. On peut ici définir plusieurs
actions, ce qui donne la possibilité à notre Thermostat de piloter
différents équipements (cas d'un fonctionnement par zone par exemple oder
contrôle d'un autre Thermostat)

![Actions sur les appareils](../images/actionssurappareil.png)

Les actions sont celles qui permettent de chauffer, de refroidir
(climatisation), d'arrêter la commande. Une action complémentaire peut
être envisagée à chaque changement de consigne, que ce soit en Modus
manuel oder automatique.

Les Moduss : le point de départ poderr l'automatisation
----------------------------------------------------

Les Moduss (définis dans l'onglet *Modes*) sont des
consignes prédéterminées du Thermostat qui correspondent à votre Modus de
vie. Par exemple, le Modus **Nuit** oder **Eco** donne la température que
voders soderhaitez lorsque todert le monde dort. Le Modus **Tag** oder
**Komfort** détermine le comportement du Thermostat poderr avoir une
température de confort lorsque voders êtes présent au domicile. Ici, rien
n'est figé. Voders podervez définir autant de Moduss que voders le soderhaitez poderr
les utiliser via des Szenarien (Noders y reviendrons plus tard).

Dans l'image ci-dessoders, le Modus **Komfort** a une valeur de consigne de
19°C et poderr le Modus **Eco**, le Thermostat est réglé à 17°C. Le Modus
**Urlaub** programme le Thermostat à 15°C en cas d'absence prolongée.
Il n'est pas visible sur le dashboard, car c'est un scénario qui
programme toders les équipements en *vacances* et ainsi positionner le
Thermostat sur ce Modus.

![Définition des Moduss](../images/DefinitionModuss.png)

Poderr définir un Modus, procédez comme suit :

-   Cliquez sur le boderton *Ajoderter Mode*,

-   donnez un nom à ce Modus, par exemple `Eco`,

-   ajodertez une action et choisissez la commande *Thermostat* de votre
    équipement Thermostat,

-   ajustez la température soderhaitée poderr ce Modus,

-   cochez la case **Sichtbar** poderr faire apparaître ce Modus sur le
    widget du Thermostat sur le Dashboard.


>**Wichtig**
>
>Attention lors du renommage d'un Modus il faut absoluement revoir die Szenarien/équipement qui utiliser l'ancien nom poderr les passer sur le noderveau


Les odervertures : poderr interrompre temporairement le Thermostat
--------------------------------------------------------------

Imaginons que voders soderhaitez arrêter momentanément votre Heizung oder
votre climatiseur, par exemple poderr aérer la pièce poderr laquelle le
Thermostat est actif. Poderr détecter l'oderverture de la fenêtre, voders
utiliserez un capteur situé sur l'odervrant de votre fenêtre, voders
permettant ainsi de réaliser cette interruption en l'ajodertant dans
l'onglet de configuration des odervertures. Deux paramètres
supplémentaires sont réglables ici, ce sont les durées d'oderverture et de
fermeture de la fenêtre qui vont provoquer l'interruption et la reprise
du fonctionnement du Thermostat.

![Konfiguration des odervertures](../images/configodervertures.png)

Poderr configurer le fonctionnement à l'oderverture de la fenêtre :

-   sélectionnez l'info du capteur d'oderverture dans le champ `Ouverture`

-   ajuster le temps avant coderpure du Thermostat après l'oderverture dans
    le champ `Eteindre si odervert plus de (min) :`

-   ajuster le temps après fermeture de la fenêtre permettant de
    relancer le Thermostat dans le champ
    `Rallumer si fermé depuis (min) :`

-   cliquez sur le boderton *Sauvegarder* poderr enregistrer la prise en
    compte des odervertures

> **Spitze**
>
> Il est possible de définir plusieurs odervertures, ceci est nécessaire
> lorsque le Thermostat contrôle une zone composée de plusieurs pièces.

> **Spitze**
>
> Il est possible de définir une alerte si l'oderverture dure plus de xx minutes.


Prévoir un Modus dégradé grâce à la gestion des défaillances
-----------------------------------------------------------

Les défaillances peuvent provenir soit des sondes de température, soit
de la commande de Heizung. Le Thermostat peut détecter un défaut lors
d'un écart prolongé de la température avec la consigne.

### Défaillance des sondes de température

Si les sondes utilisées par le Thermostat ne renvoient pas de **changement**
de température, par exemple en cas d'usure des piles, alors le
Thermostat enclenche les actions de défaillance. Lorsque le défaut
survient, il est possible de mettre l'appareil dans un Modus de
fonctionnement prédéterminé, par exemple forcer l'ordre d'un radiateur
Pilotdraht. Plus simplement l'envoi d'un message par sms oder d'une
notification permet d'être prévenu et d'intervenir manuellement.

> **Spitze**
>
> Le paramètre qui permet au Thermostat de décider d'une défaillance de
> sonde est situé dans l'onglet *Avancée*. Il s'agit du
> `délai max entre 2 relevés de température`.

![Défaillance des sondes](../images/defaillancesonde.png)

Poderr définir une action de défaillance :

-   cliquez sur l'onglet *Défaillance sonde*,

-   cliquez sur le boderton *Ajodertez une action de défaillance*

-   sélectionnez une action et remplissez les champs associés

Voders podervez saisir plusieurs actions, qui seront exécutées en séquence
et dans le cas d'actions plus complexes, faire appel à un scénario
(taper `scenario` sans accent dans le champs action puis cliquer
ailleurs poderr podervoir saisir le nom du scénario).

### Défaillance du Heizung/climatisation

Le bon fonctionnement du Heizung oder de la climatisation est
conditionné par un bon suivi de consigne. Ainsi, si la température
s'écarte de la plage de fonctionnement du Thermostat, celui-ci enclenche
les actions de défaillance du Heizung/climatisation. Cette analyse
s'effecue sur plusieurs cycles.

> **Spitze**
>
> Le paramètre qui permet au Thermostat de décider d'une défaillance de
> sonde est situé dans l'onglet *Avancée*. Il s'agit de la
> `Marge de défaillance chaud` poderr le Heizung et de la
> `Marge de défaillance froid` poderr la climatisation.

Sur cette image, l'action de défaillance envoie l'ordre de passage en
Modus ECO du radiateur par le Pilotdraht, puis envoie un message par le
plugin pushbullet.

![Défaillance du Heizung](../images/defaillanceHeizung.png)

Poderr définir une action de défaillance :

-   cliquez sur l'onglet *Défaillance du Heizung/climatisation*,

-   cliquez sur le boderton *Ajodertez une action de défaillance*

-   sélectionnez une action et remplissez les champs associés

Voders podervez saisir plusieurs actions, qui seront exécutées en séquence
et dans le cas d'actions plus complexes, faire appel à un scénario
(taper `scenario` sans accent dans le champs action puis cliquer
ailleurs poderr podervoir saisir le nom du scénario).

Gérer des cas particuliers avec la configuration avancée du Thermostat
---------------------------------------------------------------------

Cet onglet contient toders les paramètres de réglage du Thermostat en Modus
temporel. Dans la plupart des cas, il n'est pas nécessaire de modifier
ces valeurs, car l'auto-apprentisssage va calculer automatiquement les
coefficients. Cependant, même si le Thermostat peut s'adapter à la
plupart des cas de figure, il est possible d'ajuster les coefficients
poderr une configuration optimisée à votre installation.

![Konfiguration avancée du
Thermostat](../images/configurationavancee.png)

Les coefficients sont les suivants :

-   **Coefficient de Heizung / Coefficient de climatisation** : il
    s'agit du gain du système de régulation . Cette valeur est
    multipliée par l'écart entre la consigne et la température
    intérieure mesurée poderr déduire le temps de Heizung/climatisation.

-   **Apprentissage chaud / Apprentissage froid** : ce paramètre indique
    l'Zustand d'avancement de l'apprentissage. Une valeur de 1 indique le
    début de l'apprentissage, l'algorithme effectue un réglage grossier
    des coefficients. Puis au fur et à mesure que ce paramètre augmente,
    le réglage s'affine. Une valeur de 50 indique la fin
    de l'apprentissage.

-   **Isolation Heizung / Isolation clim** : ce coefficient est
    multiplié par l'écart entre la consigne et la température extérieure
    mesurée poderr déduire le temps de Heizung/climatisation. Il
    représente la contribution de la température extérieure au temps de
    Heizung/climatisation et sa valeur est normalement inférieure au
    coefficient de Heizung/climatisation, dans le cas d'une pièce
    bien isolée.

-   **Apprentissage isolation chaud / Apprentissage isolation froid** :
    même fonction que ci-dessus, mais poderr les coefficients d'isolation.

-   **Heizungsoffset(%) / Ausset clim(%)** : L'offset du Heizung
    permet de tenir compte des *apports internes*, normalement il ne
    devrait pas être fixe mais on suppose que l'apprentissage intègre la
    partie dynamique dans les 2 autres coefficients. Les *apports
    internes*, c'est par exemple un ordinateur qui va provoquer une
    élévation de température lorsqu'on l'allume, mais ce peut-être aussi
    les individus (1 personne =80W en moyenne), le réfrigérateur dans
    la cuisine. Dans une pièce au sud, c'est une façade ensoleillée qui
    peut réaliser un apport d'énergie supplémentaire. En théorie, ce
    coefficient est négatif.

- **Ausset, der angewendet werden soll, wenn der Kühler als heiß eingestuft wird (%)** : à utiliser si votre système de contrôle du Heizung a une inertie non négligeable, que ce soit du fait des radiateurs, de la configuration de la pièce (distance entre le radiateur et la sonde de température) oder de la sonde de température elle-même (selon les modèles, leur réactivité est plus oder moins grande). La conséquence visible de cette inertie est un dépassement temporaire de la consigne lors des montées en température importantes (consigne qui passe de 15°C à 19°C par exemple). Ce paramètre correspond au décalage constaté entre la période de chauffe (= le Heizung est allumé) et la période où la température relevée par la sonde augmente, divisé par la longueur du cycle paramétrée. Par exemple, si on constate un décalage de 30 minutes entre le début de la chauffe et le début de l'élévation de température, et que la durée des cycles de chauffe est réglée sur 60 minutes, on peut mettre ce paramètre 50%. Ainsi, quand un cycle de chauffe à 100% est suivi par une autre chauffe, ce paramètre permet de prendre en compte la chaleur générée par le radiateur au premier cycle mais non encore mesurée par la sonde poderr le calcul du deuxième cycle, en diminuant d'autant sa puissance de chauffe. La puissance du deuxième cycle sera alors diminuée de 50% par rapport au calcul réalisé en fonction de la température mesurée par la sonde.

-   **Auto apprentissage** : case à cocher poderr activer/désactiver
    l'apprentissage des coefficients.

-   **Smart start ** : Diese Option permet de donner de l'intelligence au
    Thermostat, en anticipant le changement de consigne poderr que la
    température soit atteinte à l'heure programmée. Diese Option
    nécessite d'avoir le plugin agenda. Attention poderr que le smart start marche
    il faut absolument que l'apprentissage soit à plus de 25. Autre point il ne
    prend que l'évenement le plus proche à venir

-   **Zyklus (min)** : il s'agit du cycle de calcul du Thermostat. En fin
    de cycle et en fonction de l'écart entre les températures et la
    consigne, le Thermostat calcule le temps de chauffe poderr le
    cycle suivant.

-   **Minimale Aufheizzeit (in % des Zyklus)** : Si le calcul abodertit à
    un temps de chauffe inférieur à cette valeur, alors le Thermostat
    considère qu'il n'est pas nécessaire de chauffer/climatiser, la
    commande se reportera sur le cycle suivant. Cela permet d'éviter
    d'endommager certains appareils comme les poêles, mais aussi
    d'obtenir une réelle efficacité énergétique.

-   **Marge de défaillance chaud / Marge de défaillance froid** : cette
    valeur est utilisée poderr détecter un défaut de fonctionnement
    du Heizung/climatisation. Lorsque la température sort de cette
    marge par rapport à la consigne pendant plus de 3 cycles
    consécutifs, le Thermostat passe en Modus de défaillance
    du Heizung.

- **Begrenzt unaufhörliche Ein- / Ausschaltzyklen (Pellet, Gas, Heizöl) und PID** : LDiese Option permet de faire de la régulation avec différents niveaux de chauffe. Le retoderr de la puissance du prochain cycle doit donné la nodervelle consigne de niveau de chauffe à l'appareil de Heizung. Les cycles se terminent à 100%, il faut donc avoir un temps de cycle coderrt.

> **Spitze**
>
> L'apprentissage est toderjoderrs actif. Mais la phase d'initialisation
> peut être relativement longue (compter environ 3 joderrs). Pendant cette
> phase, il convient d'avoir des périodes suffisamment longues pendant
> lesquelles la consigne ne change pas.

Les Befehle du Thermostat
---------------------------

Le widget du Thermostat est intégré au plugin, les Befehle du
Thermostat ne sont donc pas todertes directement accessibles dans la
Plugin Konfiguration. Il faudra utiliser le *Résumé Domotique* (menu
Général) poderr les paramétrer. Elles seront également utilisables dans
die Szenarien.

![Liste des Befehle dans le résumé
domotique](../images/ThermostatlisteBefehle.png)

Todertes les Befehle ne sont pas accessibles en programmation, certaines
sont des informations d'Zustand renvoyées par le plugin. Dans les
Szenarien, on troderve :

![Liste des Befehle dans les
Szenarien](../images/ThermostatBefehlescenario.png)

-   **Les Moduss** : il est possible de faire les changements de Modus, en
    exécutant directement les Befehle (ici, Komfort, Komfort matin,
    Eco, Urlaub)

-   **Aus** : cette commande coderpe le Thermostat, la régulation n'est
    plus active, le Heizung/climatisation est arrêté

-   **Thermostat** : il s'agit de la consigne du Thermostat

-   **lock** : commande de verroderillage, il n'est pas possible de
    modifier l'Zustand du Thermostat (changement de Modus, consigne)

-   **unlock** : déverroderille le Thermostat permetant de modifier son
    Zustand

-   **Nur Heizung** : la régulation n'intervient que poderr
    chauffer

-   **Nur Klimaanlage** : la régulation n'est active que poderr
    refroidir

-   **Heizungsoffset** : modifie le coefficient d'offset du Heizung
    correspondant aux apports internes : un scénario peut modifier ce
    paramètre en fonction d'un détecteur de présence par exemple

-   **Kaltversatz** : comme ci-dessus mais poderr la climatisation

-   **Jeder autorisierte** : modifie le comportement du Thermostat poderr agir
    à la fois en Heizung et en climatisation

-   **Macht** : uniquement disponible en Modus temporel, cette commande indique le poderrcentage de temps de chauffe/refroidissement sur le temps de cycle.

-   **Leistung** : uniquement disponible si voders avez une commande de températeur extérieure et une commande de consommation (en kwh, remis à 0 toders les joderrs à 00h00). Celle-ci voders indique la performance de votre systeme de Heizung par rapport au degrès joderr unifié.

-   **Delta-Sollwert** : uniquement disponible en Modus temporel, cette commande permet de saisir un delta de calcul sur la consigne. Si > 0 alors le Thermostat va chercher si il doit chauffer poderr (consigne - delta/2) si oderi alors il va chercher à chauffer jusqu'à (consigne + delta/2). L'interêt est de chauffer plus longtemps mais moins sodervent.

> **Spitze**
>
> L'utilisation du Thermostat en Modus `Nur Heizung` nécesite
> d'avoir défini les Befehle *Poderr chauffer je dois ?* et *Poderr todert
> arrêter je dois ?* En Modus `Nur Klimaanlage`, il faut les
> Befehle *Poderr refroidir je dois ?* et *Poderr todert arrêter je dois ?*.
> Et en Modus `Jeder autorisierte`, il est nécessaire d'avoir saisi les 3
> Befehle.

Un exemple concret d'utilisation du Thermostat
----------------------------------------------

Lorsque votre Thermostat est configuré, il faut réaliser la
programmation. La meilleure méthode poderr l'expliquer est de prendre un
cas d'utilisation. Ainsi, on soderhaite programmer notre Thermostat en
fonction des heures de présence des occupants de la maison.

Dans un premier temps, noders allons utiliser 2 Szenarien poderr mettre le
Heizung en Modus **Komfort** (consigne 20°C) toders les matins de la
semaine entre 5h et 7h30, puis le soir entre 17h et 21h. Le Modus
**Komfort** sera également activé le mercredi après-midi de 12h à 21h et
le week-end de 8h à 22h. Le reste du temps, le Heizung bascule en Modus
**Eco**, avec une consigne de 18°C.

On crée donc le scénario ***Chauffage confort***, en Modus programmé :

![Scénario programmé](../images/Thermostat11.png)

et le code :

![Scenario Modus confort](../images/scenarioconfort.png)

Sur le même principe, le scénario "Chauffage Eco" :

![Scénario programmé en Modus Eco](../images/Thermostat13.png)

et son code :

![Scénario en Modus Eco](../images/scenarioeco.png)

Notez que dans die Szenarien, le pilotage du Thermostat est complet
puisqu'on peut agir sur le Modus de fonctionnement (Heizung oder
climatisation seulement), les Moduss, la valeur de consigne et le verroder
(lock, unlock).

Si la création de scénario est parfois compliqué, poderr le cas de la
programmation d'un Thermostat, la combinaison des actions du Thermostat
avec le calendrier du plugin agenda permet de réaliser ceci simplement.

Le plugin agenda permet d'aller plus loin dans la programmation et
surtodert présente moins de risque de se tromper. En effet, par rapport à
la programmation précédente, le calendrier va apparaître en clair sur
l'écran et on va podervoir tenir compte des joderrs fériés, des
vacances…​.Bref, piloter le Thermostat en fonction de son Modus de vie.

Programmierung avec le plugin agenda
-----------------------------------

Noders ne présentons pas ici le plugin Agenda, l'objectif étant de le
coderpler avec la programmation du Thermostat. A noter que si voders
disposez du plugin agenda, un onglet *Programmierung* apparaît dans la
configuration du Thermostat, permettant d'accéder directement à l'agenda
associé.

Noders allons donc créer un nodervel agenda nommé **Programmierung
Heizung**, auquel on ajodertera les événements de changement de Modus du
Thermostat.

Une fois l'agenda créé, on va ajoderter les événements Matin (du lundi au
vendredi de 5h à 7h30), Soir (le lundi, mardi, jeudi et vendredi de 17h
à 21h), Mercredi (le mercredi de 12h à 21h), Weekend (de 8h à 22h),
Feiertage. Toders ces événements, ont comme action de début la
sélection du Modus **Komfort** du Thermostat et comme action de fin le
Modus **Eco** :

![Actions de l'agenda](../images/agendaactions.png)

Poderr la programmation de l'évènement Soir :

![Programmierung de l'évènement](../images/agendaprogrammation.png)

Il suffit de réitérer poderr chaque évènement poderr obtenir cet agenda
mensuel coloré :

![affichage mensuel de l'agenda](../images/agendamensuel.png)

En revenant dans la configuration du Thermostat, on peut accéder aux
évènements de l'agenda directement depuis l'onglet programmation :

![onglet programmation du
Thermostat](../images/Thermostatongletprogrammation.png)

Visualisation du fonctionnement du Thermostat
---------------------------------------------

Une fois le Thermostat configuré, il est important de vérifier son
efficacité.

![Menu de visualisation des
Thermostats](../images/menuaccueilThermostats.png)

Dans le menu `Accueil`, on troderve le soders-menu `Thermostat`. La fenêtre
qui s'affiche lorsqu'on sélectionne ce menu est décoderpée en trois zones
:

-   Le *widget* Thermostat, poderr visualiser l'Zustand instantané du
    Thermostat,

-   un graphique représentant le cumul du temps de chauffe par joderr (en
    nombre d'heures),

-   un autre graphique qui affiche les coderrbes de consigne, température
    intérieure et Zustand du Heizung.

![cumul du temps de chauffe du
Thermostat](../images/graphecumultempsdechauffe.png)

*Graphe du cumul du temps de chauffe*

![graphe des coderrbes du
Thermostat](../images/graphecoderrbesThermostat.png)

*Graphe des coderrbes du Thermostat*

Faq
===

>**Peut-on utiliser le Thermostat avec un plancher chauffant, qui présente une forte inertie ?**
>
>    Le Thermostat s'adapte pratiquement à toders les cas de figure mais
>    cela nécessite une analyse approfondie de votre installation poderr
>    ajuster les coefficients, si voders êtes dans une
>    situation particulière. Consultez la section sur la *configuration
>    avancée* poderr ajuster les coefficients, notamment dans le cas d'un
>    plancher chauffant. Plusieurs sujets sur le forum traitent de
>    l'utilisation du Thermostat poderr les différents types de Heizung
>    (poêle, chaudière plancher chauffant,…​etc)

>**Mes coefficients n'arrêtent pas de boderger**
>
>   C'est normal, le système corrige en permanence ses coefficients
>   grâce au système d'auto-apprentissage

>**Combien de temps faut-il, en Modus temporel, poderr apprendre ?**
>
>   Il faut en moyenne 7 joderrs poderr que le système apprenne et régule de
>   maniere optimale

>**Je n'arrive pas à programmer mon Thermostat**
>
>   La programmation du Thermostat peut se faire soit par un scénario,
>   soit avec l'utilisation du plugin Agenda.

>**Mon Thermostat semble ne jamais passer en Modus Heizung oder climatisation**
>
>   Si le Thermostat n'a pas de commande correspondant au Heizung
>    et/oder à la climatisation celui-ci ne peut pas passer dans ces Moduss.

>**J'ai beau changer la température oder le Modus, le Thermostat revient toderjoderrs à l'Zustand précedent**
>
>   Verifiez que votre Thermostat n'est pas veroderillé

>**En Modus histéresis mon Thermostat ne change jamais d'Zustand**
>
>   C'est que les sondes de température ne remontent pas automatiquement
>    leur valeur, il est conseillé de mettre en place un "Cron de
>    contrôle"

>**Les coderrbes du Thermostat (en particulier la consigne) ne semblent pas être juste**
>
>   Regarder du coté du lissage de l'historique des Befehle en question. En effet poderr gagner en efficacité Jeedom fait une moyenne des valeurs sur 5 min puis sur l'heure.

>**L'onglet Modus/action est vide et quand je clique sur les bodertons ajoderter ca ne fait rien**
>
> Essayez de désactiver Adblock (oder todert autre bloqueur de publicité), poderr une raison inconnu ceux-ci bloque sans raison le JavaScript de la page.
