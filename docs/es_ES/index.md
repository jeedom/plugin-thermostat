Descripción
===========

Ce plugin permet de créer et gérer des Termostatos por piloter le
Calefacción de votre domicile. Il fonctionne selon 2 modos, au choix :

-   le modo **hystéresis** correspond à l'allumage et l'extinction du
    Calefacción en fonction de la température intérieure, par rapport à un
    seuil correspondant à la consigne. L'hystéresis permet d'éviter des
    commutations trop fréquentes lorsque la température est autor
    la consigne.

<!-- -->

-   le modo **temporel** calcule un porcentage de Calefacción sur un
    cycle temporel prédéfini, en tenant compte des écarts entre la
    consigne et les températures intérieure et extérieure (isolation).
    Ce modo est plus précis, dispose d'un apprentissage permettant
    d'ajuster automatiquement les coefficients mais peut nécessiter
    quelques réglages manuels por l'adapter à votre installation.
    Important por que le modo temporel marche il faut absolument une
    sonde de température intérieure ET extérieure.

Configuración
=============

Ce plugin est destiné à la création de Termostatos dans Jeedom. Il
permet de piloter le Calefacción, la climatisation o les deux à la fois.

L'intérêt par rapport à un Termostato classique, c'est qu'il va povoir
s'intégrer totalement dans votre installation domotique. Outre la
régulation de température, car c'est bien ce qu'on lui demande en
premier lieu, le Termostato peut interagir avec tos les équipements de
la maison.

Parmi ses caractéristiques, on trove :

-   la prise en compte de la température extérieure, par conséquent du
    coefficient d'isolation de la maison,

-   un système de régulation qui apprend por optimiser la régulation,

-   la possibilité de gérer les ovrants por débrayer le Termostato,

-   la gestion des défaillances des équipements, sondes de température
    et appareils de Calefacción,

-   une programmation complète avec le plugin agenda, avec notamment la
    possibilité d'anticiper le changement de consigne por que la
    température soit atteinte à l'heure programmée (smart start)

Dans un premier temps, nos allons vos montrer la mise en œuvre, puis
détailler les différents réglages de la configuration du Termostato et
enfin, au travers de quelques cas d'utilisation, comment on peut
l'enrichir en combinaison avec d'autres plugins o à l'aide de
Escenarios.

La configuration en quelques clics
----------------------------------

Le Termostato de Jeedom est très puissant mais por une utilisation
traditionnelle, sa mise en œuvre est vraiment simple et rapide, à partir
du moment où on a compris les étapes essentielles :

-   définition du Moteur de Termostato (hystérésis o temporel). C'est
    le choix de l'algorithme de régulation.

-   la configuration et la plage de fonctionnement : Calefacción
    uniquement, climatisation o bien les deux , températures min et
    max d'utilisation.

-   La définition des actions que le Termostato doit exécuter por
    chauffer, refroidir o arrêter.

On trove ensuite différents onglets :

-   La configuration des modos définit des températures de
    consignes prédéterminées. Par exemple, le modo confort à 20°C, eco
    à 18°C. Il peut y avoir aussi jor, nuit, vacances, absence,…​vos
    commencez à entrevoir ici les possibilités de personnalisation
    du plugin.

-   Por affiner le modo de fonctionnement du Termostato, vos allez
    povoir également configurer des overtures qui vont interrompre
    temporairement la régulation (par exemple, une fenêtre overte peut
    arrêter le Calefacción). La définition de cette interruption
    s'effectue ici simplement.

-   La gestion des modos de défaillance por les sondes de température
    o por le Calefacción permet de définir des actions à exécuter por
    un modo dégradé.

-   L'onglet Configuración avancée permet d'ajuster les paramètres de
    régulation du Calefacción.

-   Si de plus, vos disposez du plugin Agenda, la programmation des
    changements de modo devient possible directement depuis
    l'onglet programmation.

Votre Termostato est maintenant opérationnel, et par l'utilisation de
Escenarios o en le combinant avec d'autres plugins (agenda,
virtuel,présence,…​), il va se fondre en doceur dans votre installation
domotique. Voilà ce que l'on obtient sur le dashboard :

![Aspect sur le dashboard](../images/Termostato.png)

Le verro présent sur le widget permet de bloquer le Termostato dans une
consigne donnée, suite à un imprévu : congés, invités,…​.


La création d'un Termostato en détail
-------------------------------------

Por créer un noveau Termostato, rendez-vos sur la page de
configuration en dérolant le menu Plugins/Bien-être et sélectionnez
Termostato. Cliquez sur le boton *Ajoter* situé en haut à gauche et
renseignez le nom sohaité por votre Termostato.

![Configuración générale](../images/Termostato_config_générale.png)

Dans un premier temps, nos allons renseigner les paramètres généraux du
Termostato. On les trove en haut à gauche, section général et il faut
préciser ici l'objet parent, l'activation et la visibilité du
Termostato, informations habituelles por tot utilisateur de jeedom.

Le choix de l'algorithme du Termostato
--------------------------------------

![Choix de l'algorithme](../images/Termostato31.png)

En évidence sur cette image le Moteur de fonctionnement du Termostato.
Il y a 2 algorithmes possibles por la régulation de température.

Lorsque vos sélectionnez le modo Hystérésis, la mise en rote de votre
Calefacción se produit dès que la température est inférieure à la consigne
moins l'hystérésis et il s'éteint dès que la température dépasse la
consigne plus l'hystérésis.

![Principe du modo hystérésis](../images/PrincipeHysteresis.png)

Par exemple, si on règle l'hystérésis à 1°C et que la valeur de consigne
vaut 19°C, alors le Calefacción s'active lorsque la température passe en
dessos de 18°C et s'arrête dès qu'il atteint 20°C.

Les paramètres à fornir sont l'hystérésis en °C et la commande qui
permet de récupérer la mesure de température. On règlera l'hystérésis en
fonction de la précision du capteur, par exemple por une sonde précise
à 0.5°C, un hystérésis de 0.2°C est un bon compromis.

> **Punta**
>
> Le paramètre hystérésis se trove dans l'onglet *avancée*.

Dans le cas du modo temporel, la commande de Calefacción o de
climatisation est définie sur un cycle prédéfini et la durée d'exécution
de la commande est fonction de l'écart entre la consigne et la
température mesurée par le capteur. L'algorithme va également calculer
le temps de chauffe (o de climatisation) sur un cycle en fonction de
l'inertie et de l'isolation de la pièce.

![Principe du modo temporel](../images/PrincipeTemporel.png)

Enfin, plus le temps de cycle sera grand, plus la régulation sera lente.
A l'inverse, un temps trop faible provoquera des commutations fréquentes
de votre système de Calefacción qui n'aura peut-être pas le temps de
chauffer le volume de la pièce efficacement. Se recomienda no
trop diminuer ce temps de cycle (les valeurs acceptables sont comprises
entre 30 et 60mn).

Ce type de régulation est plus optimisé, il améliore le confort et
permet de réaliser des économies d'énergie substantielles.

La configuration
----------------

Outre le moteur de fonctionnement du Termostato, vos povez décider si
le Termostato est utilisé en modo Calefacción, climatisation o les deux.
Puis vos indiquez sa plage d'utilisation : les températures minimale et
maximale vont définir les valeurs possibles de consigne accessibles sur
le widget.

![Configuración du fonctionnement](../images/configFonctionnement.png)

Ensuite, il faut préciser les Comandos qui permettent de mesurer la
température et de piloter le Calefacción o la climatisation. Notez que le
moteur temporel a besoin de connaître la température extérieure. Si usted
ne disposez pas d'un capteur extérieur, celle-ci peut être fornie par
le plugin météo.

![Sélection des sondes](../images/selectionsondes.png)

> **Punta**
>
> Les champs `Borne de température inférieure` et
> `Borne de température supérieure` définissent la plage de
> fonctionnement du Termostato en dehors de laquelle une défaillance du
> Calefacción est enclenchée. Voir ci dessos le paragraphe sur les
> actions de défaillance.

Por la commande du radiateur o du climatiseur, il est décrit dans
l'onglet *Actions*. On peut ici définir plusieurs
actions, ce qui donne la possibilité à notre Termostato de piloter
différents équipements (cas d'un fonctionnement par zone par exemple o
contrôle d'un autre Termostato)

![Actions sur les appareils](../images/actionssurappareil.png)

Les actions sont celles qui permettent de chauffer, de refroidir
(climatisation), d'arrêter la commande. Une action complémentaire peut
être envisagée à chaque changement de consigne, que ce soit en modo
manuel o automatique.

Les modos : le point de départ por l'automatisation
----------------------------------------------------

Les modos (définis dans l'onglet *Modes*) sont des
consignes prédéterminées du Termostato qui correspondent à votre modo de
vie. Par exemple, le modo **Nuit** o **Eco** donne la température que
vos sohaitez lorsque tot le monde dort. Le modo **Día** o
**Confort** détermine le comportement du Termostato por avoir une
température de confort lorsque vos êtes présent au domicile. Ici, rien
n'est figé. Vos povez définir autant de modos que vos le sohaitez por
les utiliser via des Escenarios (Nos y reviendrons plus tard).

Dans l'image ci-dessos, le modo **Confort** a une valeur de consigne de
19°C et por le modo **Eco**, le Termostato est réglé à 17°C. Le modo
**Vacaciones** programme le Termostato à 15°C en cas d'absence prolongée.
Il n'est pas visible sur le dashboard, car c'est un scénario qui
programme tos les équipements en *vacances* et ainsi positionner le
Termostato sur ce modo.

![Définition des modos](../images/Definitionmodos.png)

Por définir un modo, procédez comme suit :

-   Cliquez sur le boton *Ajoter Mode*,

-   donnez un nom à ce modo, par exemple `Eco`,

-   ajotez une action et choisissez la commande *Termostato* de votre
    équipement Termostato,

-   ajustez la température sohaitée por ce modo,

-   cochez la case **Visible** por faire apparaître ce modo sur le
    widget du Termostato sur le Dashboard.


>**Importante**
>
>Attention lors du renommage d'un modo il faut absoluement revoir los escenarios/équipement qui utiliser l'ancien nom por les passer sur le noveau


Les overtures : por interrompre temporairement le Termostato
--------------------------------------------------------------

Imaginons que vos sohaitez arrêter momentanément votre Calefacción o
votre climatiseur, par exemple por aérer la pièce por laquelle le
Termostato est actif. Por détecter l'overture de la fenêtre, vos
utiliserez un capteur situé sur l'ovrant de votre fenêtre, vos
permettant ainsi de réaliser cette interruption en l'ajotant dans
l'onglet de configuration des overtures. Deux paramètres
supplémentaires sont réglables ici, ce sont les durées d'overture et de
fermeture de la fenêtre qui vont provoquer l'interruption et la reprise
du fonctionnement du Termostato.

![Configuración des overtures](../images/configovertures.png)

Por configurer le fonctionnement à l'overture de la fenêtre :

-   sélectionnez l'info du capteur d'overture dans le champ `Ouverture`

-   ajuster le temps avant copure du Termostato après l'overture dans
    le champ `Eteindre si overt plus de (min) :`

-   ajuster le temps après fermeture de la fenêtre permettant de
    relancer le Termostato dans le champ
    `Rallumer si fermé depuis (min) :`

-   cliquez sur le boton *Sauvegarder* por enregistrer la prise en
    compte des overtures

> **Punta**
>
> Il est possible de définir plusieurs overtures, ceci est nécessaire
> lorsque le Termostato contrôle une zone composée de plusieurs pièces.

> **Punta**
>
> Il est possible de définir une alerte si l'overture dure plus de xx minutes.


Prévoir un modo dégradé grâce à la gestion des défaillances
-----------------------------------------------------------

Les défaillances peuvent provenir soit des sondes de température, soit
de la commande de Calefacción. Le Termostato peut détecter un défaut lors
d'un écart prolongé de la température avec la consigne.

### Défaillance des sondes de température

Si les sondes utilisées par le Termostato ne renvoient pas de **changement**
de température, par exemple en cas d'usure des piles, alors le
Termostato enclenche les actions de défaillance. Lorsque le défaut
survient, il est possible de mettre l'appareil dans un modo de
fonctionnement prédéterminé, par exemple forcer l'ordre d'un radiateur
Cable piloto. Plus simplement l'envoi d'un message par sms o d'une
notification permet d'être prévenu et d'intervenir manuellement.

> **Punta**
>
> Le paramètre qui permet au Termostato de décider d'une défaillance de
> sonde est situé dans l'onglet *Avancée*. Il s'agit du
> `délai max entre 2 relevés de température`.

![Défaillance des sondes](../images/defaillancesonde.png)

Por définir une action de défaillance :

-   cliquez sur l'onglet *Défaillance sonde*,

-   cliquez sur le boton *Ajotez une action de défaillance*

-   sélectionnez une action et remplissez les champs associés

Vos povez saisir plusieurs actions, qui seront exécutées en séquence
et dans le cas d'actions plus complexes, faire appel à un scénario
(taper `scenario` sans accent dans le champs action puis cliquer
ailleurs por povoir saisir le nom du scénario).

### Défaillance du Calefacción/climatisation

Le bon fonctionnement du Calefacción o de la climatisation est
conditionné par un bon suivi de consigne. Ainsi, si la température
s'écarte de la plage de fonctionnement du Termostato, celui-ci enclenche
les actions de défaillance du Calefacción/climatisation. Cette analyse
s'effecue sur plusieurs cycles.

> **Punta**
>
> Le paramètre qui permet au Termostato de décider d'une défaillance de
> sonde est situé dans l'onglet *Avancée*. Il s'agit de la
> `Marge de défaillance chaud` por le Calefacción et de la
> `Marge de défaillance froid` por la climatisation.

Sur cette image, l'action de défaillance envoie l'ordre de passage en
modo ECO du radiateur par le Cable piloto, puis envoie un message par le
plugin pushbullet.

![Défaillance du Calefacción](../images/defaillanceCalefacción.png)

Por définir une action de défaillance :

-   cliquez sur l'onglet *Défaillance du Calefacción/climatisation*,

-   cliquez sur le boton *Ajotez une action de défaillance*

-   sélectionnez une action et remplissez les champs associés

Vos povez saisir plusieurs actions, qui seront exécutées en séquence
et dans le cas d'actions plus complexes, faire appel à un scénario
(taper `scenario` sans accent dans le champs action puis cliquer
ailleurs por povoir saisir le nom du scénario).

Gérer des cas particuliers avec la configuration avancée du Termostato
---------------------------------------------------------------------

Cet onglet contient tos les paramètres de réglage du Termostato en modo
temporel. Dans la plupart des cas, il n'est pas nécessaire de modifier
ces valeurs, car l'auto-apprentisssage va calculer automatiquement les
coefficients. Cependant, même si le Termostato peut s'adapter à la
plupart des cas de figure, il est possible d'ajuster les coefficients
por une configuration optimisée à votre installation.

![Configuración avancée du
Termostato](../images/configurationavancee.png)

Les coefficients sont les suivants :

-   **Coefficient de Calefacción / Coefficient de climatisation** : il
    s'agit du gain du système de régulation . Cette valeur est
    multipliée par l'écart entre la consigne et la température
    intérieure mesurée por déduire le temps de Calefacción/climatisation.

-   **Apprentissage chaud / Apprentissage froid** : ce paramètre indique
    l'Estado d'avancement de l'apprentissage. Une valeur de 1 indique le
    début de l'apprentissage, l'algorithme effectue un réglage grossier
    des coefficients. Puis au fur et à mesure que ce paramètre augmente,
    le réglage s'affine. Une valeur de 50 indique la fin
    de l'apprentissage.

-   **Isolation Calefacción / Isolation clim** : ce coefficient est
    multiplié par l'écart entre la consigne et la température extérieure
    mesurée por déduire le temps de Calefacción/climatisation. Il
    représente la contribution de la température extérieure au temps de
    Calefacción/climatisation et sa valeur est normalement inférieure au
    coefficient de Calefacción/climatisation, dans le cas d'une pièce
    bien isolée.

-   **Apprentissage isolation chaud / Apprentissage isolation froid** :
    même fonction que ci-dessus, mais por les coefficients d'isolation.

-   **Compensación de calentamiento(%) / Apagadoset clim(%)** : L'offset du Calefacción
    permet de tenir compte des *apports internes*, normalement il ne
    devrait pas être fixe mais on suppose que l'apprentissage intègre la
    partie dynamique dans les 2 autres coefficients. Les *apports
    internes*, c'est par exemple un ordinateur qui va provoquer une
    élévation de température lorsqu'on l'allume, mais ce peut-être aussi
    les individus (1 personne =80W en moyenne), le réfrigérateur dans
    la cuisine. Dans une pièce au sud, c'est une façade ensoleillée qui
    peut réaliser un apport d'énergie supplémentaire. En théorie, ce
    coefficient est négatif.

- **Desplazamiento a aplicar si el radiador se considera caliente (%)** : à utiliser si votre système de contrôle du Calefacción a une inertie non négligeable, que ce soit du fait des radiateurs, de la configuration de la pièce (distance entre le radiateur et la sonde de température) o de la sonde de température elle-même (selon les modèles, leur réactivité est plus o moins grande). La conséquence visible de cette inertie est un dépassement temporaire de la consigne lors des montées en température importantes (consigne qui passe de 15°C à 19°C par exemple). Ce paramètre correspond au décalage constaté entre la période de chauffe (= le Calefacción est allumé) et la période où la température relevée par la sonde augmente, divisé par la longueur du cycle paramétrée. Par exemple, si on constate un décalage de 30 minutes entre le début de la chauffe et le début de l'élévation de température, et que la durée des cycles de chauffe est réglée sur 60 minutes, on peut mettre ce paramètre 50%. Ainsi, quand un cycle de chauffe à 100% est suivi par une autre chauffe, ce paramètre permet de prendre en compte la chaleur générée par le radiateur au premier cycle mais non encore mesurée par la sonde por le calcul du deuxième cycle, en diminuant d'autant sa puissance de chauffe. La puissance du deuxième cycle sera alors diminuée de 50% par rapport au calcul réalisé en fonction de la température mesurée par la sonde.

-   **Auto apprentissage** : case à cocher por activer/désactiver
    l'apprentissage des coefficients.

-   **Inicio inteligente** : Esta opcion permet de donner de l'intelligence au
    Termostato, en anticipant le changement de consigne por que la
    température soit atteinte à l'heure programmée. Esta opcion
    nécessite d'avoir le plugin agenda. Attention por que le smart start marche
    il faut absolument que l'apprentissage soit à plus de 25. Autre point il ne
    prend que l'évenement le plus proche à venir

-   **Ciclo (min)** : il s'agit du cycle de calcul du Termostato. En fin
    de cycle et en fonction de l'écart entre les températures et la
    consigne, le Termostato calcule le temps de chauffe por le
    cycle suivant.

-   **Tiempo de calentamiento mínimo (% del ciclo)** : Si le calcul abotit à
    un temps de chauffe inférieur à cette valeur, alors le Termostato
    considère qu'il n'est pas nécessaire de chauffer/climatiser, la
    commande se reportera sur le cycle suivant. Cela permet d'éviter
    d'endommager certains appareils comme les poêles, mais aussi
    d'obtenir une réelle efficacité énergétique.

-   **Marge de défaillance chaud / Marge de défaillance froid** : cette
    valeur est utilisée por détecter un défaut de fonctionnement
    du Calefacción/climatisation. Lorsque la température sort de cette
    marge par rapport à la consigne pendant plus de 3 cycles
    consécutifs, le Termostato passe en modo de défaillance
    du Calefacción.

- **Limita los ciclos de encendido / apagado incesante (pellet, gas, fuel oil) y PID** : LEsta opcion permet de faire de la régulation avec différents niveaux de chauffe. Le retor de la puissance du prochain cycle doit donné la novelle consigne de niveau de chauffe à l'appareil de Calefacción. Les cycles se terminent à 100%, il faut donc avoir un temps de cycle cort.

> **Punta**
>
> L'apprentissage est tojors actif. Mais la phase d'initialisation
> peut être relativement longue (compter environ 3 jors). Pendant cette
> phase, il convient d'avoir des périodes suffisamment longues pendant
> lesquelles la consigne ne change pas.

Les Comandos du Termostato
---------------------------

Le widget du Termostato est intégré au plugin, les Comandos du
Termostato ne sont donc pas totes directement accessibles dans la
Configuración del plugin. Il faudra utiliser le *Résumé Domotique* (menu
Général) por les paramétrer. Elles seront également utilisables dans
los escenarios.

![Liste des Comandos dans le résumé
domotique](../images/TermostatolisteComandos.png)

Totes les Comandos ne sont pas accessibles en programmation, certaines
sont des informations d'Estado renvoyées par le plugin. Dans les
Escenarios, on trove :

![Liste des Comandos dans les
Escenarios](../images/TermostatoComandosscenario.png)

-   **Les modos** : il est possible de faire les changements de modo, en
    exécutant directement les Comandos (ici, Confort, Confort matin,
    Eco, Vacaciones)

-   **Apagado** : cette commande cope le Termostato, la régulation n'est
    plus active, le Calefacción/climatisation est arrêté

-   **Termostato** : il s'agit de la consigne du Termostato

-   **lock** : commande de verroillage, il n'est pas possible de
    modifier l'Estado du Termostato (changement de modo, consigne)

-   **unlock** : déverroille le Termostato permetant de modifier son
    Estado

-   **Solo calefacción** : la régulation n'intervient que por
    chauffer

-   **Solo aire acondicionado** : la régulation n'est active que por
    refroidir

-   **Compensación de calentamiento** : modifie le coefficient d'offset du Calefacción
    correspondant aux apports internes : un scénario peut modifier ce
    paramètre en fonction d'un détecteur de présence par exemple

-   **Compensación en frío** : comme ci-dessus mais por la climatisation

-   **Permitir todo** : modifie le comportement du Termostato por agir
    à la fois en Calefacción et en climatisation

-   **Potencia** : uniquement disponible en modo temporel, cette commande indique le porcentage de temps de chauffe/refroidissement sur le temps de cycle.

-   **Rendimiento** : uniquement disponible si vos avez une commande de températeur extérieure et une commande de consommation (en kwh, remis à 0 tos les jors à 00h00). Celle-ci vos indique la performance de votre systeme de Calefacción par rapport au degrès jor unifié.

-   **Punto de ajuste delta** : uniquement disponible en modo temporel, cette commande permet de saisir un delta de calcul sur la consigne. Si > 0 alors le Termostato va chercher si il doit chauffer por (consigne - delta/2) si oi alors il va chercher à chauffer jusqu'à (consigne + delta/2). L'interêt est de chauffer plus longtemps mais moins sovent.

> **Punta**
>
> L'utilisation du Termostato en modo `Solo calefacción` nécesite
> d'avoir défini les Comandos *Por chauffer je dois ?* et *Por tot
> arrêter je dois ?* En modo `Solo aire acondicionado`, il faut les
> Comandos *Por refroidir je dois ?* et *Por tot arrêter je dois ?*.
> Et en modo `Permitir todo`, il est nécessaire d'avoir saisi les 3
> Comandos.

Un exemple concret d'utilisation du Termostato
----------------------------------------------

Lorsque votre Termostato est configuré, il faut réaliser la
programmation. La meilleure méthode por l'expliquer est de prendre un
cas d'utilisation. Ainsi, on sohaite programmer notre Termostato en
fonction des heures de présence des occupants de la maison.

Dans un premier temps, nos allons utiliser 2 Escenarios por mettre le
Calefacción en modo **Confort** (consigne 20°C) tos les matins de la
semaine entre 5h et 7h30, puis le soir entre 17h et 21h. Le modo
**Confort** sera également activé le mercredi après-midi de 12h à 21h et
le week-end de 8h à 22h. Le reste du temps, le Calefacción bascule en modo
**Eco**, avec une consigne de 18°C.

On crée donc le scénario ***Chauffage confort***, en modo programmé :

![Scénario programmé](../images/Termostato11.png)

et le code :

![Scenario modo confort](../images/scenarioconfort.png)

Sur le même principe, le scénario "Chauffage Eco" :

![Scénario programmé en modo Eco](../images/Termostato13.png)

et son code :

![Scénario en modo Eco](../images/scenarioeco.png)

Notez que dans los escenarios, le pilotage du Termostato est complet
puisqu'on peut agir sur le modo de fonctionnement (Calefacción o
climatisation seulement), les modos, la valeur de consigne et le verro
(lock, unlock).

Si la création de scénario est parfois compliqué, por le cas de la
programmation d'un Termostato, la combinaison des actions du Termostato
avec le calendrier du plugin agenda permet de réaliser ceci simplement.

Le plugin agenda permet d'aller plus loin dans la programmation et
surtot présente moins de risque de se tromper. En effet, par rapport à
la programmation précédente, le calendrier va apparaître en clair sur
l'écran et on va povoir tenir compte des jors fériés, des
vacances…​.Bref, piloter le Termostato en fonction de son modo de vie.

Programación avec le plugin agenda
-----------------------------------

Nos ne présentons pas ici le plugin Agenda, l'objectif étant de le
copler avec la programmation du Termostato. A noter que si vos
disposez du plugin agenda, un onglet *Programación* apparaît dans la
configuration du Termostato, permettant d'accéder directement à l'agenda
associé.

Nos allons donc créer un novel agenda nommé **Programación
Calefacción**, auquel on ajotera les événements de changement de modo du
Termostato.

Une fois l'agenda créé, on va ajoter les événements Matin (du lundi au
vendredi de 5h à 7h30), Soir (le lundi, mardi, jeudi et vendredi de 17h
à 21h), Mercredi (le mercredi de 12h à 21h), Weekend (de 8h à 22h),
Días feriados. Tos ces événements, ont comme action de début la
sélection du modo **Confort** du Termostato et comme action de fin le
modo **Eco** :

![Actions de l'agenda](../images/agendaactions.png)

Por la programmation de l'évènement Soir :

![Programación de l'évènement](../images/agendaprogrammation.png)

Il suffit de réitérer por chaque évènement por obtenir cet agenda
mensuel coloré :

![affichage mensuel de l'agenda](../images/agendamensuel.png)

En revenant dans la configuration du Termostato, on peut accéder aux
évènements de l'agenda directement depuis l'onglet programmation :

![onglet programmation du
Termostato](../images/Termostatoongletprogrammation.png)

Visualisation du fonctionnement du Termostato
---------------------------------------------

Une fois le Termostato configuré, il est important de vérifier son
efficacité.

![Menu de visualisation des
Termostatos](../images/menuaccueilTermostatos.png)

Dans le menu `Accueil`, on trove le sos-menu `Termostato`. La fenêtre
qui s'affiche lorsqu'on sélectionne ce menu est décopée en trois zones
:

-   Le *widget* Termostato, por visualiser l'Estado instantané du
    Termostato,

-   un graphique représentant le cumul du temps de chauffe par jor (en
    nombre d'heures),

-   un autre graphique qui affiche les corbes de consigne, température
    intérieure et Estado du Calefacción.

![cumul du temps de chauffe du
Termostato](../images/graphecumultempsdechauffe.png)

*Graphe du cumul du temps de chauffe*

![graphe des corbes du
Termostato](../images/graphecorbesTermostato.png)

*Graphe des corbes du Termostato*

Preguntas frecuentes
===

>**Peut-on utiliser le Termostato avec un plancher chauffant, qui présente une forte inertie ?**
>
>    Le Termostato s'adapte pratiquement à tos les cas de figure mais
>    cela nécessite une analyse approfondie de votre installation por
>    ajuster les coefficients, si vos êtes dans une
>    situation particulière. Consultez la section sur la *configuration
>    avancée* por ajuster les coefficients, notamment dans le cas d'un
>    plancher chauffant. Plusieurs sujets sur le forum traitent de
>    l'utilisation du Termostato por les différents types de Calefacción
>    (poêle, chaudière plancher chauffant,…​etc)

>**Mes coefficients n'arrêtent pas de boger**
>
>   C'est normal, le système corrige en permanence ses coefficients
>   grâce au système d'auto-apprentissage

>**Combien de temps faut-il, en modo temporel, por apprendre ?**
>
>   Il faut en moyenne 7 jors por que le système apprenne et régule de
>   maniere optimale

>**Je n'arrive pas à programmer mon Termostato**
>
>   La programmation du Termostato peut se faire soit par un scénario,
>   soit avec l'utilisation du plugin Agenda.

>**Mon Termostato semble ne jamais passer en modo Calefacción o climatisation**
>
>   Si le Termostato n'a pas de commande correspondant au Calefacción
>    et/o à la climatisation celui-ci ne peut pas passer dans ces modos.

>**J'ai beau changer la température o le modo, le Termostato revient tojors à l'Estado précedent**
>
>   Verifiez que votre Termostato n'est pas veroillé

>**En modo histéresis mon Termostato ne change jamais d'Estado**
>
>   C'est que les sondes de température ne remontent pas automatiquement
>    leur valeur, il est conseillé de mettre en place un "Cron de
>    contrôle"

>**Les corbes du Termostato (en particulier la consigne) ne semblent pas être juste**
>
>   Regarder du coté du lissage de l'historique des Comandos en question. En effet por gagner en efficacité Jeedom fait une moyenne des valeurs sur 5 min puis sur l'heure.

>**L'onglet modo/action est vide et quand je clique sur les botons ajoter ca ne fait rien**
>
> Essayez de désactiver Adblock (o tot autre bloqueur de publicité), por une raison inconnu ceux-ci bloque sans raison le JavaScript de la page.
