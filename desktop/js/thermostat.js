
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


$(".eqLogic").delegate(".listCmdInfo", 'click', function () {
    var el = $(this).closest('.form-group').find('.eqLogicAttr');
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        if (el.attr('data-concat') == 1) {
            el.atCaret('insert', result.human);
        } else {
            el.value(result.human);
        }
    });
});

$('body').delegate('.rename', 'click', function () {
    var el = $(this);
    bootbox.prompt("{{Nouveau nom ?}}", function (result) {
        if (result !== null) {
            el.text(result);
            el.closest('.mode').find('.modeAttr[data-l1key=name]').value(result);
        }
    });
});

$("body").delegate(".listCmdAction", 'click', function () {
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.expressionAttr[data-l1key=cmd]');
    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function (result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function (html) {
            el.closest('.' + type).find('.actionOptions').html(html);
        });

    });
});

$('.addAction').on('click', function () {
    addAction({}, $(this).attr('data-type'));
});

$('.addWindow').on('click', function () {
    addWindow({});
});

$('.eqLogicAttr[data-l1key=configuration][data-l2key=engine]').on('change', function () {
    $('.engine').hide();
    $('.' + $(this).value()).show();
});

$("body").delegate(".listCmdInfoWindow", 'click', function () {
    var el = $(this).closest('.form-group').find('.expressionAttr[data-l1key=cmd]');
    jeedom.cmd.getSelectModal({cmd: {type: 'info', subtype: 'binary'}}, function (result) {
        el.value(result.human);
    });
});

$('.addMode').on('click', function () {
    bootbox.prompt("{{Nom du mode ?}}", function (result) {
        if (result !== null) {
            addMode({name: result});
        }
    });
});

$("body").delegate(".addModeAction", 'click', function () {
    addModeAction({}, $(this).closest('.mode').find('.div_modeAction'));
});

$("body").delegate(".removeMode", 'click', function () {
    var el = $(this);
    bootbox.confirm('{{Etes-vous sûr de vouloir supprimer ce mode}} ?', function (result) {
        el.closest('.mode').remove();
    });
});

$('body').delegate('.cmdAction.expressionAttr[data-l1key=cmd]', 'focusout', function (event) {
    var type = $(this).attr('data-type');
    var expression = $(this).closest('.' + type).getValues('.expressionAttr');
    var el = $(this);
    jeedom.cmd.displayActionOption($(this).value(), init(expression[0].options), function (html) {
        el.closest('.' + type).find('.actionOptions').html(html);
    });

});

$("body").delegate('.bt_removeAction', 'click', function () {
    var type = $(this).attr('data-type');
    $(this).closest('.' + type).remove();
});

$('#bt_configureMode').on('click', function () {
    $('#md_modal').dialog({title: "{{Configuration des modes}}"});
    $('#md_modal').load('index.php?v=d&plugin=thermostat&modal=configure.mode').dialog('open');
});


function saveEqLogic(_eqLogic) {
    if (!isset(_eqLogic.configuration)) {
        _eqLogic.configuration = {};
    }
    _eqLogic.configuration.heating = $('#div_heat .heat').getValues('.expressionAttr');
    _eqLogic.configuration.cooling = $('#div_cool .cool').getValues('.expressionAttr');
    _eqLogic.configuration.stoping = $('#div_stop .stop').getValues('.expressionAttr');
    _eqLogic.configuration.window = $('#div_window .window').getValues('.expressionAttr');
    _eqLogic.configuration.orderChange = $('#div_orderChange .orderChange').getValues('.expressionAttr');
    _eqLogic.configuration.existingMode = [];
    $('#div_modes .mode').each(function () {
        var existingMode = $(this).getValues('.modeAttr');
        existingMode = existingMode[0];
        existingMode.actions = $(this).find('.modeAction').getValues('.expressionAttr');
        _eqLogic.configuration.existingMode.push(existingMode);
    });
    return _eqLogic;
}

function printEqLogic(_eqLogic) {
    $('#div_heat').empty();
    $('#div_cool').empty();
    $('#div_stop').empty();
    $('#div_modes').empty();
    $('#div_window').empty();
    $('#div_orderChange').empty();
    if (isset(_eqLogic.configuration)) {
        if (isset(_eqLogic.configuration.heating)) {
            for (var i in _eqLogic.configuration.heating) {
                addAction(_eqLogic.configuration.heating[i], 'heat');
            }
        }
        if (isset(_eqLogic.configuration.cooling)) {
            for (var i in _eqLogic.configuration.cooling) {
                addAction(_eqLogic.configuration.cooling[i], 'cool');
            }
        }
        if (isset(_eqLogic.configuration.stoping)) {
            for (var i in _eqLogic.configuration.stoping) {
                addAction(_eqLogic.configuration.stoping[i], 'stop');
            }
        }
        if (isset(_eqLogic.configuration.orderChange)) {
            for (var i in _eqLogic.configuration.orderChange) {
                addAction(_eqLogic.configuration.orderChange[i], 'orderChange');
            }
        }

        if (isset(_eqLogic.configuration.window)) {
            for (var i in _eqLogic.configuration.window) {
                addWindow(_eqLogic.configuration.window[i]);
            }
        }
        if (isset(_eqLogic.configuration.existingMode)) {
            for (var i in _eqLogic.configuration.existingMode) {
                addMode(_eqLogic.configuration.existingMode[i]);
            }
        }
    }
}

function addMode(_mode) {
    if (init(_mode) == '') {
        return;
    }
    var div = '<form class="form-horizontal mode">';
    div += '<fieldset>';
    div += '<legend>';
    div += '<span class="rename cursor">' + _mode.name + '</span>';
    div += ' (Visible : <input type="checkbox"  class="modeAttr" data-l1key="isVisible" checked /> )';
    div += ' <a class="btn btn-danger btn-xs removeMode pull-right"><i class="fa fa-minus-circle"></i> Supprimer mode</a> ';
    div += ' <a class="btn btn-default btn-xs addModeAction pull-right"><i class="fa fa-plus-circle"></i> Ajouter action</a> ';
    div += ' </legend>';
    div += '<input class="modeAttr" data-l1key="name"  style="display : none;" value="' + _mode.name + '"/>';
    div += ' <div class="div_modeAction">';
    div += ' </div>';
    div += '</fieldset> ';
    div += '</form>';
    $('#div_modes').append(div);
    $('#div_modes .mode:last').setValues(_mode, '.modeAttr');
    if (isset(_mode.actions)) {
        for (var i in _mode.actions) {
            if (init(_mode.actions[i].cmd) != '') {
                addModeAction(_mode.actions[i], $('#div_modes .mode:last .div_modeAction'));
            }
        }
    }
}

function addModeAction(_modeAction, _el) {
    var div = '<div class="modeAction">';
    div += '<div class="form-group ">';
    div += '<label class="col-lg-1 control-label">Action</label>';
    div += '<div class="col-lg-1">';
    div += '<a class="btn btn-default btn-sm listCmdAction" data-type="modeAction"><i class="fa fa-list-alt"></i></a>';
    div += '</div>';
    div += '<div class="col-lg-3">';
    div += '<input class="expressionAttr form-control input-sm cmdAction" data-l1key="cmd" data-type="modeAction" />';
    div += '</div>';
    div += '<div class="col-lg-6 actionOptions">';
    div += jeedom.cmd.displayActionOption(init(_modeAction.cmd, ''), _modeAction.options);
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<i class="fa fa-minus-circle pull-right cursor bt_removeAction" data-type="modeAction"></i>';
    div += '</div>';
    div += '</div>';
    _el.append(div);
    _el.find('.modeAction:last').setValues(_modeAction, '.expressionAttr');
}


function addAction(_action, _type) {
    var div = '<div class="' + _type + '">';
    div += '<div class="form-group ">';
    div += '<label class="col-lg-1 control-label">Action</label>';
    div += '<div class="col-lg-1">';
    div += '<a class="btn btn-default btn-sm listCmdAction" data-type="' + _type + '"><i class="fa fa-list-alt"></i></a>';
    div += '</div>';
    div += '<div class="col-lg-3">';
    div += '<input class="expressionAttr form-control input-sm cmdAction" data-l1key="cmd" data-type="' + _type + '" />';
    div += '</div>';
    div += '<div class="col-lg-6 actionOptions">';
    div += jeedom.cmd.displayActionOption(init(_action.cmd, ''), _action.options);
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<i class="fa fa-minus-circle pull-right cursor bt_removeAction" data-type="' + _type + '"></i>';
    div += '</div>';
    div += '</div>';
    $('#div_' + _type).append(div);
    $('#div_' + _type + ' .' + _type + ':last').setValues(_action, '.expressionAttr');
}


function addWindow(_info) {
    var div = '<div class="window">';
    div += '<div class="form-group ">';
    div += '<label class="col-lg-1 control-label">Ouverture</label>';
    div += '<div class="col-lg-1">';
    div += '<a class="btn btn-default btn-sm listCmdInfoWindow"><i class="fa fa-list-alt"></i></a>';
    div += '</div>';
    div += '<div class="col-lg-3">';
    div += '<input class="expressionAttr form-control input-sm cmdInfo" data-l1key="cmd" />';
    div += '</div>';
    div += '<label class="col-lg-2 control-label">Eteindre si ouvert plus de (min) :</label>';
    div += '<div class="col-lg-1">';
    div += '<input class="expressionAttr form-control input-sm cmdInfo" data-l1key="stopTime" />';
    div += '</div>';
    div += '<label class="col-lg-2 control-label">Rallumer si fermé depuis (min) :</label>';
    div += '<div class="col-lg-1">';
    div += '<input class="expressionAttr form-control input-sm cmdInfo" data-l1key="restartTime"/>';
    div += '</div>';
    div += '<div class="col-lg-1">';
    div += '<i class="fa fa-minus-circle pull-right cursor bt_removeAction" data-type="window"></i>';
    div += '</div>';
    div += '</div>';
    $('#div_window').append(div);
    $('#div_window .window:last').setValues(_info, '.expressionAttr');
}

