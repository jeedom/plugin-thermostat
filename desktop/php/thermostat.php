<?php
if (!isConnect('admin')) {
    throw new Exception('{{Error 401 Unauthorized}}');
}
sendVarToJS('eqType', 'thermostat');
?>
<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un thermostat}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('thermostat') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <div class="row">
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Général}}</legend>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">{{Nom du thermostat}}</label>
                            <div class="col-lg-6">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du thermostat}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" >{{Objet parent}}</label>
                            <div class="col-lg-6">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (object::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">{{Activer}}</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
                            </div>
                            <label class="col-lg-4 control-label">{{Visible}}</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">{{Moteur}}</label>
                            <div class="col-lg-6">
                                <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="engine" placeholder="" >
                                    <option value="temporal">Temporel</option>
                                    <option value="hysteresis">Hysteresis</option>
                                </select>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Configuration}}</legend>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Autoriser}}</label>
                            <div class="col-lg-6">
                                <select class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="allow_mode" title="{{Veuillez préciser les actions que le thermostat à le droit de faire en terme de chauffage et refroidissement.}}">
                                    <option value="all">Tout</option>
                                    <option value="heat">Chauffage uniquement</option>
                                    <option value="cool">Climatisation uniquement</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group expertModeVisible">
                            <label class="col-lg-3 control-label">{{Température minimale (°C)}}</label>
                            <div class="col-lg-6">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_min" title="{{Précisez l'écart de température que le thermostat est autorisé à piloter}}"/>
                            </div>
                        </div>
                        <div class="form-group expertModeVisible">
                            <label class="col-lg-3 control-label">{{Température maximale (°C)}}</label>
                            <div class="col-lg-6">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_max" title="{{Précisez l'écart de température que le thermostat est autorisé à piloter}}"/>
                            </div>
                        </div>
                        <div class="form-group engine temporal">
                            <div class='expertModeVisible'>
                                <label class="col-lg-3 control-label">{{Cycle (min)}}</label>
                                <div class="col-lg-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="cycle" title="{{Durée des cycles de chauffe/climatisation (ne peut être inferieur à 15 min)}}"/>
                                </div>
                                <label class="col-lg-5 control-label">{{Temps de chauffe minimum (% du cycle)}}</label>
                                <div class="col-lg-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="minCycleDuration" title="{{% minimum de cycle a faire (sinon la mise en marche du chauffage est reporté au cyle suivant)}}" value="5"/>
                                </div>
                            </div>

                        </div>
                    </fieldset> 
                </form>
            </div>
        </div>
        <hr/>
        <form class="form-horizontal">
            <fieldset>
                <div class="form-group engine temporal">
                    <div class="expertModeVisible">
                        <div class="alert alert-warning">
                            {{Pour une meilleur régulation, il est conseillé de ne pas toucher à ces coefficients, car ils seront calculés et mis à jour automatiquement}}
                        </div>
                        <label class="col-lg-2 control-label">{{Coefficient chauffage}}</label>
                        <div class="col-lg-2">
                            <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_heat" />
                        </div>
                        <label class="col-lg-1 control-label">{{Clim}}</label>
                        <div class="col-lg-2">
                            <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_cool" />
                        </div>
                        <label class="col-lg-1 control-label">{{Isolation}}</label>
                        <div class="col-lg-2">
                            <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor" />
                        </div>
                    </div>
                </div>
                <div class="form-group engine hysteresis" style="display: none;">
                    <label class="col-lg-2 control-label">{{Hystéresis (°C)}}</label>
                    <div class="col-lg-2">
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_threshold" placeholder="1"/>
                    </div>
                    <div class='expertModeVisible'>
                        <label class="col-lg-2 control-label">{{Cron de controle}}</label>
                        <div class="col-lg-2">
                            <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_cron" title="{{Cron de vérification, si votre thermostat ne démarre ou ne s'arrête pas correctement mettez en place cette vérification}}"/>
                        </div>
                        <div class="col-lg-1">
                            <i class="fa fa-question-circle cursor bt_pageHelp floatright" data-name="cronSyntaxe"></i>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    {{Veuillez ajouter vos sondes de température}}
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Température intérieure}}</label>
                    <div class="col-lg-9">
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor" data-concat="1"/>
                    </div>
                    <div class="col-lg-1">
                        <a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
                    </div>
                </div>
                <div class="form-group engine temporal">
                    <label class="col-lg-2 control-label">{{Température extérieure}}</label>
                    <div class="col-lg-9">
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_outdoor" data-concat="1"/>
                    </div>
                    <div class="col-lg-1">
                        <a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
                    </div>
                </div>
            </fieldset> 
        </form>
        <hr/>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#configureAction" data-toggle="tab">Configuration des actions</a></li>
            <li><a href="#configureMode" data-toggle="tab">Configuration des modes</a></li>
            <li><a href="#configureWindows" data-toggle="tab">Configuration des ouvertures</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="configureAction">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>
                            {{Pour chauffer je dois ?}}
                            <a class="btn btn-danger btn-xs pull-right addAction" data-type="heat" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
                        </legend>
                        <div id="div_heat">

                        </div>
                    </fieldset> 
                </form>  

                <form class="form-horizontal">
                    <fieldset>
                        <legend>
                            {{Pour refroidir je dois ?}}
                            <a class="btn btn-primary btn-xs pull-right addAction" data-type="cool" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
                        </legend>
                        <div id="div_cool">

                        </div>
                    </fieldset> 
                </form>

                <form class="form-horizontal">
                    <fieldset>
                        <legend>
                            {{Pour tout arrêter je dois ?}}
                            <a class="btn btn-default btn-xs pull-right addAction" data-type="stop" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
                        </legend>
                        <div id="div_stop">

                        </div>
                    </fieldset> 
                </form>
                <form class="form-horizontal">
                    <fieldset>
                        <legend>
                            {{A chaque changement de consigne je dois aussi faire ?}}
                            <a class="btn btn-default btn-xs pull-right addAction" data-type="orderChange" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
                        </legend>
                        <div id="div_orderChange">

                        </div>
                    </fieldset> 
                </form>
            </div>
            <div class="tab-pane" id="configureMode">
                <form class="form-horizontal">
                    <fieldset>
                        <br/>
                        <div class="alert alert-info">
                            {{Avec les modes, vous pouvez rajouter à votre thermostat des consignes prédéfinies. Par exemple un mode confort qui déclenche une action sur votre thermostat avec une température de consigne de 20°C}}
                            <a class="btn btn-success addMode pull-right" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> Ajouter mode</a>
                        </div>
                        <br/><br/>
                        <div id="div_modes"></div>
                    </fieldset> 
                </form> 
            </div>
            <div class="tab-pane" id="configureWindows">
                <form class="form-horizontal">
                    <fieldset>
                        <br/>
                        <div class="alert alert-info">
                            {{La déclaration des ouvertures concernées par votre thermostat (porte, fenêtre...) permettra au thermostat de réguler la température en conséquence.}}
                            <a class="btn btn-success addWindow pull-right" data-type="window" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> Ajouter ouverture</a>
                        </div>
                        <br/><br/>
                        <div id="div_window"></div>
                    </fieldset> 
                </form> 
            </div>
        </div>

        <hr/>
        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php include_file('desktop', 'thermostat', 'js', 'thermostat'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
