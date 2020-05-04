Description
===========

This plugin allows yor to create and manage thermostats to control the
heating your home. It operates in 2 modes, your choice :

-   The fashion **hysteresis** corresponds to switching on and off
    heating as a function of the interior temperature, relative to a
    threshold corresponding to the setpoint. Hysteresis helps prevent
    too frequent switching when the temperature is around
    setpoint.

<!-- -->

-   The fashion **temporel** calculates a heating percentage on a
    predefined time cycle, taking into account the differences between the
    setpoint and indoor and outdoor temperatures (insulation).
    This fashion is more precise, has a learning allowing
    automatically adjust the coefficients but may require
    some manual adjustments to adapt it to your installation.
    Important for the time fashion to work, yor absolutely need a
    indoor AND outdoor temperature sensor.

Configuration
=============

This plugin is intended for the creation of thermostats in Jeedom. he
can control heating, air conditioning or both.

The advantage compared to a conventional Thermostat is that it will
fully integrate into your home automation system. Besides the
temperature regulation, because that's what we ask him in
first, the Thermostat can interact with all the equipment
the House.

Among its features are :

-   taking into account the outside temperature, consequently the
    house insulation coefficient,

-   a regulatory system that learns to optimize regulation,

-   the possibility of managing the doors to disengage the thermostat,

-   equipment failure management, temperature probes
    and heaters,

-   complete programming with the agenda plugin, including in particular the
    possibility of anticipating the change of setpoint so that the
    temperature is reached at the scheduled time (smart start)

First, we will show yor the implementation, then
detahe the different settings of the Thermostat configuration and
finally, through some use cases, how we can
enrich it in combination with other plugins or using
Scenarios.

Setup in a few clicks
----------------------------------

The Jeedom Thermostat is very powerful but for use
traditional, its implementation is really simple and fast, from
from the moment we understand the essential steps :

-   definition of Thermostat motor (hysteresis or time). It is
    the choice of the regulation algorithm.

-   configuration and operating range : chauffage
    only, air conditioning or both, min and
    max use.

-   Defining the actions that the Thermostat should perform to
    heat, cool or stop.

Then there are different tabs :

-   The configuration of the modes defines temperatures of
    predetermined instructions. For example, comfort fashion at 20 ° C, eco
    at 18 ° C. There can also be day, night, vacation, absence,… you
    start to see here the possibilities for customization
    plugin.

-   To refine the operating fashion of the thermostat, yor will
    also be able to configure openings that will interrupt
    temporarily regulating (for example, an open window may
    stop heating). The definition of this interruption
    is done here simply.

-   Management of failure modes for temperature sensors
    or for heating allows to define actions to be executed for
    a degraded fashion.

-   The Advanced Setup tab allows yor to adjust the parameters of
    heating regulation.

-   If in addition, yor have the Agenda plugin, the programming of
    fashion changes becomes possible directly from
    the programming tab.

Your Thermostat is now operational, and by using
scenarios or by combining it with other plugins (agenda,
presence, ...), it will blend smoothly into your installation
Automation. This is what we get on the dashboard :

![Aspect sur le dashboard](../images/thermostat.png)

The lock on the widget allows yor to lock the Thermostat in a
instruction given following an unforeseen event : leave, guests,….


The creation of a Thermostat in detail
-------------------------------------

To create a new thermostat, go to the page
configuration by pulling down the Plugins / Well-being menu and select
Thermostat. Click on the * Add * button at the top left and
enter the desired name for your Thermostat.

![Setup générale](../images/thermostat_config_générale.png)

First, we will inform the general parameters of the
thermostat. They are found at the top left, general section and it is necessary
specify here the parent object, the activation and the visibility of the
thermostat, usual information for any jeedom user.

The choice of Thermostat algorithm
--------------------------------------

![Choix de l'algorithme](../images/thermostat31.png)

Highlighted in this image is the Thermostat operating motor..
There are 2 possible algorithms for temperature regulation.

When yor select the Hysteresis mode, the start-up of your
heating occurs as soon as the temperature is below the set point
minus the hysteresis and it turns off as soon as the temperature exceeds the
setpoint plus hysteresis.

![Principe du fashion hystérésis](../images/PrincipeHysteresis.png)

For example, if yor set the hysteresis to 1 ° C and the setpoint
is 19 ° C, then heating is activated when the temperature drops
below 18 ° C and stops as soon as it reaches 20 ° C.

The parameters to be supplied are the hysteresis in ° C and the command which
allows to retrieve the temperature measurement. We will adjust the hysteresis in
depending on the accuracy of the sensor, for example for a precise probe
at 0.5 ° C, a hysteresis of 0.2 ° C is a good compromise.

> **Tip**
>
> The hysteresis parameter is found in the * advanced tab*.

In the case of time mode, the heating or
air conditioning is defined on a predefined cycle and the duration of execution
of the command is a function of the difference between the setpoint and the
temperature measured by the sensor. The algorithm will also calculate
the heating (or cooling) time on a cycle depending on
inertia and room insulation.

![Principe du fashion temporal](../images/PrincipeTemporel.png)

Finally, the longer the cycle time, the slower the regulation.
Conversely, too short a time will cause frequent switching
of your heating system which may not have time to
heat the room volume effectively. It is recommended not to
reduce this cycle time too much (acceptable values are included
between 30 and 60 minutes).

This type of regulation is more optimized, it improves comfort and
allows substantial energy savings.

The configuration
----------------

In addition to the Thermostat operating motor, yor can decide whether
the Thermostat is used in heating, air conditioning or both.
Then yor indicate its range of use : minimum temperatures and
maximum will define the possible setpoint values accessible on
the widget.

![Setup du fonctionnement](../images/configFonctionnement.png)

Next, specify the commands that measure the
temperature and control the heating or air conditioning. Note that the
time motor needs to know the outside temperature. If you
do not have an external sensor, this can be provided by
the weather plugin.

![Sélection des sondes](../images/selectionsondes.png)

> **Tip**
>
> The fields ``Lower temperature limit`` and
> ``Upper temperature limit`` define the range of
> Thermostat operation outside which a failure of the
> heating is on. See below the paragraph on
> default actions.

For the control of the radiator or air conditioner, it is described in
* Actions tab*. Here we can define several
actions, which gives our Thermostat the ability to control
different equipment (case of operation by zone for example or
control of another thermostat)

![Actions sur les appareils](../images/actionssurappareil.png)

Actions are those that heat, cool
(air conditioning), stop the command. A complementary action can
be considered at each setpoint change, whether in mode
manual or automatic.

The trends : the starting point for automation
----------------------------------------------------

The modes (defined in the * Modes * tab) are
predetermined Thermostat setpoints that correspond to your fashion of
life. For example, the fashion **Nuit** or **Eco** give the temperature that
yor wish when everyone sleeps. The fashion **Jour** ou
**Confort** determines the behavior of the Thermostat to have a
comfort temperature when yor are at home. Nothing here
is not frozen. Yor can define as many modes as yor want for
use them via scenarios (we'll come back to that later).

In the image below, the fashion **Confort** has a setpoint of
19 ° C and for fashion **Eco**, the Thermostat is set at 17 ° C. The mode
**Vacances** programs the Thermostat at 15 ° C in the event of prolonged absence.
It is not visible on the dashboard, because it is a scenario that
program all the equipment on * vacation * and thus position the
Thermostat in this fashion.

![Définition des modes](../images/Definitionmodes.png)

To define a mode, proceed as follows :

-   Click on the button * Add Mode*,

-   give a name to this mode, for example ``Eco``,

-   add an action and choose the * Thermostat * command on your
    Thermostat equipment,

-   adjust the desired temperature for this mode,

-   Check the box **Visible** to bring up this fashion on the
    Thermostat widget on the dashboard.


>**IMPORTANT**
>
>Attention during the renaming of a fashion it is absolutely necessary to review the scenarios / equipment which use the old name to pass them on the new


The openings : to temporarily interrupt the thermostat
--------------------------------------------------------------

Imagine that yor want to temporarily stop your heating or
your air conditioner, for example to ventilate the room for which the
Thermostat is active. To detect the opening of the window, you
use a sensor located on the opening of your window, you
thus making it possible to carry out this interruption by adding it in
openings configuration tab. Two parameters
additional are adjustable here, these are the opening times and
closing of the window which will cause the interruption and the resumption
how the Thermostat works.

![Setup des ouvertures](../images/configouvertures.png)

To configure the operation when the window is opened :

-   select the opening sensor info in the ``Opening`` field

-   adjust the time before the Thermostat switches off after opening in
    the field ``Switch off if open more than (min) :``

-   adjust the time after closing the window allowing
    restart the Thermostat in the field
    ```` Switch on again if closed for (min) :``

-   click on the button * Save * to save the take
    account of openings

> **Tip**
>
> It is possible to define several openings, this is necessary
> when the Thermostat controls an area made up of several rooms.

> **Tip**
>
> It is possible to set an alert if the opening lasts more than xx minutes.


Predict a degraded fashion thanks to failure management
-----------------------------------------------------------

Faults can come from either temperature sensors or
of the heating control. The Thermostat can detect a fault during
a prolonged deviation of the temperature from the setpoint.

### Temperature probe failure

If the probes used by the Thermostat do not return any **changement**
temperature, for example if the batteries are worn out, then the
Thermostat initiates fault actions. When the fault
occurs, it is possible to put the device in a
predetermined operation, for example forcing the order of a radiator
Pilot wire. More simply sending a text message or a
notification allows to be warned and to intervene manually.

> **Tip**
>
> The parameter that allows the Thermostat to decide on a failure of
> probe is located in the * Advanced tab*. It's about
> ``max delay between 2 temperature readings``.

![Défaillance des sondes](../images/defaillancesonde.png)

To define a failure action :

-   click on the * Probe failure tab*,

-   click on the button * Add a failure action*

-   select an action and fill in the associated fields

Yor can enter several actions, which will be executed in sequence
and in the case of more complex actions, use a scenario
(type ``scenario`` without accent in the action field then click
elsewhere to be able to enter the name of the scenario).

### Heating / air conditioning failure

The proper functioning of the heating or air conditioning is
conditioned by good follow-up of deposit. So if the temperature
deviates from the operating range of the thermostat, it switches on
heating / air conditioning failure actions. This analysis
takes place over several cycles.

> **Tip**
>
> The parameter that allows the Thermostat to decide on a failure of
> probe is located in the * Advanced tab*. It's about the
> ``Hot failure margin`` for heating and
> ``Cold failure margin`` for air conditioning.

In this image, the failure action sends the order to switch to
ECO fashion of the radiator by the pilot wire, then sends a message by the
pushbullet plugin.

![Défaillance heating](../images/defaillancechauffage.png)

To define a failure action :

-   click on the tab * Heating / air conditioning failure*,

-   click on the button * Add a failure action*

-   select an action and fill in the associated fields

Yor can enter several actions, which will be executed in sequence
and in the case of more complex actions, use a scenario
(type ``scenario`` without accent in the action field then click
elsewhere to be able to enter the name of the scenario).

Manage special cases with the advanced Thermostat configuration
---------------------------------------------------------------------

This tab contains all the parameters for adjusting the Thermostat in mode
temporal. In most cases, there is no need to modify
these values, because self-learning will automatically calculate the
coefficients. However, even if the Thermostat can adapt to the
in most cases, it is possible to adjust the coefficients
for an optimized configuration for your installation.

![Setup avancée du
Thermostat](../ images / configurationavancee.png)

The coefficients are as follows :

-   **Heating coefficient / Cooling coefficient** : il
    is the gain of the regulatory system . This value is
    multiplied by the difference between the setpoint and the temperature
    interior measured to deduct heating / cooling time.

-   **Hot learning / Cold learning** : this parameter indicates
    learning progress. A value of 1 indicates the
    start of learning, algorithm performs rough adjustment
    coefficients. Then as this parameter increases,
    the setting is refined. A value of 50 indicates the end
    of learning.

-   **Heating insulation / Air conditioning insulation** : this coefficient is
    multiplied by the difference between the setpoint and the outside temperature
    measured to deduct the heating / cooling time. he
    represents the contribution of the outside temperature to the time of
    heating / air conditioning and its value is normally less than
    heating / air conditioning coefficient, in the case of a room
    well insulated.

-   **Learn hot insulation / Learn cold insulation** :
    same function as above, but for the insulation coefficients.

-   **Heating offset (%) / Air conditioning offset (%)** : The heating offset
    allows to take into account * internal contributions *, normally it does not
    should not be fixed but it is assumed that learning integrates the
    dynamic part in the other 2 coefficients. Contributions
    internal *, it is for example a computer which will cause a
    temperature rise when turned on, but it may also be
    individuals (1 person = 80W on average), the refrigerator in
    the kitchen. In a room to the south, it is a sunny facade which
    can provide additional energy. In theory, this
    coefficient is negative.

- **Offset to be applied if the radiator is considered hot (%)** : to be used if your heating control system has a significant inertia, whether due to the radiators, the configuration of the room (distance between the radiator and the temperature probe) or the temperature probe itself ( depending on the model, their reactivity is more or less). The visible consequence of this inertia is a temporary overshoot of the set point during significant temperature increases (set point which goes from 15 ° C to 19 ° C for example). This parameter corresponds to the difference observed between the heating period (= heating is on) and the period when the temperature measured by the probe increases, divided by the length of the configured cycle.. For example, if there is a difference of 30 minutes between the start of heating and the start of temperature rise, and the duration of the heating cycles is set to 60 minutes, we can set this parameter 50%. Thus, when a 100% heating cycle is followed by another heating, this parameter allows to take into account the heat generated by the radiator in the first cycle but not yet measured by the probe for the calculation of the second cycle, by reducing d '' its heating power. The power of the second cycle will then be reduced by 50% compared to the calculation made according to the temperature measured by the probe..

-   **Self learning** : check box to activate / deactivate
    learning the coefficients.

-   **Smart start** : This option gives intelligence to the
    thermostat, anticipating the setpoint change so that the
    temperature reached at the scheduled time. This option
    requires the agenda plugin. Attention for the smart start to work
    learning must be more than 25. Another point
    takes that nearest event to come

-   **cycle (min)** : this is the Thermostat calculation cycle. Finally
    cycle and as a function of the difference between the temperatures and the
    setpoint, the Thermostat calculates the heating time for the
    next cycle.

-   **Minimal heating duration (% cycle)** : If the calculation results in
    a heating time lower than this value, then the thermostat
    considers that it is not necessary to heat / cool, the
    command will carry over to the next cycle. This avoids
    damage certain devices such as stoves, but also
    achieve real energy efficiency.

-   **Hot Failure Margin / Cold Failure Margin** : cette
    value is used to detect a malfunction
    heating / air conditioning. When the temperature comes out of this
    margin compared to the setpoint for more than 3 cycles
    the Thermostat switches to failure mode
    heating.

- **Limits incessant on / off cycles (pellet, gas, fuel oil) and PID** : This option allows yor to regulate with different heating levels. The return of power from the next cycle must give the new heating level setpoint to the heater. Cycles end at 100%, so have a short cycle time.

> **Tip**
>
> Learning is always active. But the initialization phase
> can be relatively long (around 3 days). During this
> phase, it is necessary to have sufficiently long periods during
> which the setpoint does not change.

Thermostat controls
---------------------------

The Thermostat widget is integrated into the plugin, the controls of the
Thermostat are therefore not all directly accessible in the
Plugin configuration. Yor will have to use the * Home Automation Summary * (menu
General) to configure them. They will also be usable in
scenarios.

![Liste des Commands dans le résumé
home automation](../ images / thermostatlistecommandes.png)

Not all commands are accessible in programming, some
are status information returned by the plugin. In the
scenarios we find :

![Liste des Commands dans les
scenarios](../ images / thermostatcommandesscenario.png)

-   **The trends** : it is possible to make fashion changes, by
    directly executing the commands (here, Comfort, Comfort morning,
    Eco, Holidays)

-   **Off** : this command cuts the thermostat, the regulation is not
    more active, heating / air conditioning is stopped

-   **Thermostat** : this is the Thermostat setpoint

-   **lock** : lock command, it is not possible to
    modify the Thermostat status (fashion change, setpoint)

-   **unlock** : unlocks the Thermostat allowing yor to change its
    état

-   **Heating only** : the regulation only intervenes for
    chauffer

-   **Air conditioning only** : regulation is only active for
    refroidir

-   **Heating offset** : modifies the offset coefficient of the heating
    corresponding to internal contributions : a scenario can change this
    parameter based on a presence detector for example

-   **Cold offset** : as above but for air conditioning

-   **Allow everything** : changes the behavior of the Thermostat to act
    both heating and air conditioning

-   **Puissance** : only available in time mode, this command indicates the percentage of heating / cooling time over the cycle time.

-   **Performance** : only available if yor have an outdoor temperature control and a consumption control (in kWh, reset to 0 every day at 00:00). This shows yor the performance of your heating system compared to the unified degree day.

-   **Delta setpoint** : only available in time mode, this command allows yor to enter a calculation delta on the setpoint. If> 0 then the Thermostat will search if it should heat for (setpoint - delta / 2) if yes then it will seek to heat up to (setpoint + delta / 2). The advantage is to heat longer but less often.

> **Tip**
>
> The use of the Thermostat in ``Heating only`` fashion requires
> to have defined the commands * To heat I must ?* and * For everything
> stop i have to ?* In ``Air conditioning only`` mode, yor must
> * To cool I have to ?* and * To stop everything I have to ?*.
> And in ``All authorized`` mode, yor must have entered the 3
> Commands.

A concrete example of using the thermostat
----------------------------------------------

When your Thermostat is configured, yor must perform the
programming. The best way to explain it is to take a
use case. So, we want to program our Thermostat in
according to the hours of presence of the occupants of the house.

First, we will use 2 scenarios to put the
heating in fashion **Confort** (setpoint 20 ° C) every morning of the
week between 5 a.m. and 7:30 a.m., then in the evening between 5 p.m. and 9 p.m.. The mode
**Confort** will also be activated on Wednesday afternoon from 12 p.m. to 9 p.m. and
weekends from 8 a.m. to 10 p.m.. The rest of the time, the heating switches to
**Eco**, with a set point of 18 ° C.

So we create the scenario ***Comfort heating***, in programmed fashion :

![Scénario programmé](../images/thermostat11.png)

and the code :

![Scenario fashion confort](../images/scenarioconfort.png)

On the same principle, the "Eco Heating" scenario" :

![Scénario programmé en fashion Eco](../images/thermostat13.png)

and its code :

![Scénario en fashion Eco](../images/scenarioeco.png)

Note that in the scenarios, the Thermostat control is complete
since we can act on the operating fashion (heating or
only), modes, setpoint and lock
(lock, unlock).

If scenario creation is sometimes complicated, for the case of
programming a thermostat, the combination of Thermostat actions
with the calendar of the agenda plugin allows to do this simply.

The agenda plugin allows yor to go further in programming and
especially presents less risk of being wrong. Indeed, compared to
previous programming, the calendar will appear in clear on
the screen and we will be able to take public holidays,
vacation .... In short, control the Thermostat according to his lifestyle.

Programming with the agenda plugin
-----------------------------------

We do not present here the Agenda plugin, the objective being to
pair with Thermostat programming. Note that if you
have the agenda plugin, a * Programming * tab appears in the
configuration of the thermostat, allowing direct access to the agenda
associé.

So we are going to create a new agenda named **Programmation
chauffage**, to which we will add the fashion change events of the
thermostat.

Once the calendar is created, we will add the Morning events (Monday to
Friday from 5 a.m. to 7:30 a.m.), Evening (Monday, Tuesday, Thursday and Friday from 5 p.m.
9 p.m.), Wednesday (Wednesday noon to 9 p.m.), Weekend (8 a.m. to 10 p.m.),
Holidays. All these events have as their starting action the
fashion selection **Confort** of the Thermostat and as an end action the
fashion **Eco** :

![Actions de l'agenda](../images/agendaactions.png)

For the programming of the Evening event :

![Programming de l'évènement](../images/agendaprogrammation.png)

Just repeat for each event to get this agenda
colorful monthly :

![affichage mensuel de l'agenda](../images/agendamensuel.png)

Returning to the Thermostat configuration, yor can access the
calendar events directly from the programming tab :

![onglet programmation du
Thermostat](../ images / Thermostat tabprogrammation.png)

Visualization of Thermostat operation
---------------------------------------------

Once the Thermostat is configured, it is important to check its
efficacité.

![Menu de visualisation des
thermostats](../ images / menuaccueilthermostats.png)

In the ``Home`` menu, there is the`` Thermostat`` submenu. The window
which is displayed when this menu is selected is divided into three areas
:

-   The Thermostat widget to view the instant status of the
    thermostat,

-   a graph representing the cumulative heating time per day (in
    number of hours),

-   another graph which displays the setpoint, temperature curves
    interior and heating status.

![cumul du temps de chauffe du
Thermostat](../ images / graphecumultempsdechauffe.png)

*Cumulative heating time graph*

![graphe des courbes du
Thermostat](../ images / graphecourbesthermostat.png)

*Thermostat curve graph*

FAQ
===

>**Can we use the Thermostat with a heated floor, which has a high inertia ?**
>
>    The Thermostat adapts practically to all cases but
>    this requires a thorough analysis of your installation to
>    adjust the coefficients, if yor are in a
>    particular situation. See the section on * configuration
>    advanced * to adjust the coefficients, especially in the case of a
>    heating floor. Several topics on the forum deal with
>    using the Thermostat for different types of heating
>    (stove, underfloor heating boiler, etc.)

>**My coefficients keep moving**
>
>   This is normal, the system constantly corrects its coefficients
>   thanks to the self-learning system

>**How long does it take, in time mode, to learn ?**
>
>   It takes on average 7 days for the system to learn and regulate
>   optimal way

>**I cannot program my thermostat**
>
>   Thermostat programming can be done either by a scenario,
>   either with the use of the Agenda plugin.

>**My Thermostat never seems to go into heating or air conditioning mode**
>
>   If the Thermostat has no control corresponding to the heating
>    and / or air conditioning it cannot switch to these modes.

>**No matter how I change the temperature or the mode, the Thermostat always returns to the previous state**
>
>   Check that your Thermostat is not locked

>**In history fashion my Thermostat never changes state**
>
>   Is that the temperature sensors do not go up automatically
>    their value, it is advisable to set up a "Cron de
>    control"

>**Thermostat curves (especially the setpoint) do not seem to be right**
>
>   Look at the smoothing side of the order history in question. Indeed to gain efficiency Jeedom averages the values over 5 min then over the hour.

>**The fashion / action tab is empty and when I click on the add buttons it does nothing**
>
> Try to disable Adblock (or any other ad blocker), for some unknown reason these block the JavaScript of the page without reason.
