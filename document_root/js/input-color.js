/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */

(function() {
  // List of supported colors
  var colorList = [
    ['000000', '000000', '000000', '000000', '003300', '006600', '009900', '00CC00', '00FF00', '330000', '333300', '336600', '339900', '33CC00', '33FF00', '660000', '663300', '666600', '669900', '66CC00', '66FF00', ],
    ['000000', '333333', '000000', '000033', '003333', '006633', '009933', '00CC33', '00FF33', '330033', '333333', '336633', '339933', '33CC33', '33FF33', '660033', '663333', '666633', '669933', '66CC33', '66FF33', ],
    ['000000', '666666', '000000', '000066', '003366', '006666', '009966', '00CC66', '00FF66', '330066', '333366', '336666', '339966', '33CC66', '33FF66', '660066', '663366', '666666', '669966', '66CC66', '66FF66', ],
    ['000000', '999999', '000000', '000099', '003399', '006699', '009999', '00CC99', '00FF99', '330099', '333399', '336699', '339999', '33CC99', '33FF99', '660099', '663399', '666699', '669999', '66CC99', '66FF99', ],
    ['000000', 'CCCCCC', '000000', '0000CC', '0033CC', '0066CC', '0099CC', '00CCCC', '00FFCC', '3300CC', '3333CC', '3366CC', '3399CC', '33CCCC', '33FFCC', '6600CC', '6633CC', '6666CC', '6699CC', '66CCCC', '66FFCC', ],
    ['000000', 'FFFFFF', '000000', '0000FF', '0033FF', '0066FF', '0099FF', '00CCFF', '00FFFF', '3300FF', '3333FF', '3366FF', '3399FF', '33CCFF', '33FFFF', '6600FF', '6633FF', '6666FF', '6699FF', '66CCFF', '66FFFF', ],
    ['000000', 'FF0000', '000000', '990000', '993300', '996600', '999900', '99CC00', '99FF00', 'CC0000', 'CC3300', 'CC6600', 'CC9900', 'CCCC00', 'CCFF00', 'FF0000', 'FF3300', 'FF6600', 'FF9900', 'FFCC00', 'FFFF00', ],
    ['000000', '00FF00', '000000', '990033', '993333', '996633', '999933', '99CC33', '99FF33', 'CC0033', 'CC3333', 'CC6633', 'CC9933', 'CCCC33', 'CCFF33', 'FF0033', 'FF3333', 'FF6633', 'FF9933', 'FFCC33', 'FFFF33', ],
    ['000000', '0000FF', '000000', '990066', '993366', '996666', '999966', '99CC66', '99FF66', 'CC0066', 'CC3366', 'CC6666', 'CC9966', 'CCCC66', 'CCFF66', 'FF0066', 'FF3366', 'FF6666', 'FF9966', 'FFCC66', 'FFFF66', ],
    ['000000', 'FFFF00', '000000', '990099', '993399', '996699', '999999', '99CC99', '99FF99', 'CC0099', 'CC3399', 'CC6699', 'CC9999', 'CCCC99', 'CCFF99', 'FF0099', 'FF3399', 'FF6699', 'FF9999', 'FFCC99', 'FFFF99', ],
    ['000000', '00FFFF', '000000', '9900CC', '9933CC', '9966CC', '9999CC', '99CCCC', '99FFCC', 'CC00CC', 'CC33CC', 'CC66CC', 'CC99CC', 'CCCCCC', 'CCFFCC', 'FF00CC', 'FF33CC', 'FF66CC', 'FF99CC', 'FFCCCC', 'FFFFCC', ],
    ['000000', 'FF00FF', '000000', '9900FF', '9933FF', '9966FF', '9999FF', '99CCFF', '99FFFF', 'CC00FF', 'CC33FF', 'CC66FF', 'CC99FF', 'CCCCFF', 'CCFFFF', 'FF00FF', 'FF33FF', 'FF66FF', 'FF99FF', 'FFCCFF', 'FFFFFF', ],
  ];

  // Finds container from event
  function getContainer(ev) {
    return ev.target.upOrSelf('.colorPickerContainerOuter');
  }

  // Table onclick handler
  function tableOnClick(ev) {
    var td = ev.target.upOrSelf('td'), color = td.getAttribute('bgColor');

    var inp = getContainer(ev).inputElement;
    inp.value = color;
    inp.style.color = color;
  }

  // Table mouse move
  function tableOnMouseMove(ev) {
    var td = ev.target.upOrSelf('td'), color = td.getAttribute('bgColor');
    var colorContainer = getContainer(ev).colorContainer;
    colorContainer.down('#ColorPreview').setStyle( { backgroundColor: color } );
    colorContainer.down('#ColorHex').value = color;
  }

  // Create color picker container
  function createPicketContainer(container) {
    if(container.colorContainer) return container.colorContainer;
    var colorContainer = container.colorContainer = (new Element('div', { className: 'colorPickerContainer' } )).update('<table border=0 cellspacing=0 cellpadding=4 width=100%>' +
        '<tr><td bgcolor="buttonface" valign=center><div style="background-color: #000000; padding: 1; height: 21px; width: 50px"><div id="ColorPreview" style="height: 100%; width: 100%"></div></div></td>  <td bgcolor="buttonface" valign=center><input type="text" id="ColorHex" value="" size=15 style="font-size: 12px"></td>  <td bgcolor="buttonface" width=100%></td> </tr>' +
        '</table><table class="colorPicker" border=0 cellspacing=1 cellpadding=0 bgcolor=#000000 style="cursor: hand;"><tbody></tbody></table>').setStyle( { display: 'none' } );
    container.appendChild(colorContainer);

    // Prepare table with colors
    var tbd = colorContainer.down('table', 1).tBodies[0];
    colorList.each(function(colorRow) {
        // Create row of colors
        var tr = new Element('tr');
        colorRow.each(function(color) { tr.appendChild((new Element('td', { bgColor: '#' + color } )).setStyle( { backgroundColor: '#' + color } )); });
        tbd.appendChild(tr);
    });

    // Row 'No color'
    tbd.appendChild(Builder.node('tr', [ Builder.node('td', { bgColor: '', style: 'background: white; text-align: center;', colSpan: 99 }, 'No color') ] ));

    tbd.observe('click', tableOnClick);
    tbd.observe('mousemove', tableOnMouseMove);
    colorContainer.observe('click', function(ev) { colorContainer.hide(); container.removeClassName('visible'); } );

    return colorContainer;
  }

  // Clicked on picker icon -> show or hide picker
  function iconOnClick(ev) {
    var container = getContainer(ev);
    var colorContainer = createPicketContainer(container);

    if(colorContainer.visible()) {
        colorContainer.hide();
        container.removeClassName('visible');
    }
    else {
        colorContainer.show();
        container.addClassName('visible');
    }
  }


  // Register it
  inputFormats.add('color', function(inp, options, format) {
    // Create outer container for input and arrow
    var img = Builder.node('span', { class: 'arrow' } );
    var container = Builder.node('div', { className: 'colorPickerContainerOuter' } );
    container.inputElement = inp;
    inp.insertAdjacentElement('afterEnd', container);
    container.appendChild(inp);
    container.appendChild(img);

    img.observe('click', iconOnClick)
  });

})();
