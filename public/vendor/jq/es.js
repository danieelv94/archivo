jqEsMX =
    {
        // separator of parts of a date (e.g. '/' in 11/05/1955)
        '/': "/",
        // separator of parts of a time (e.g. ':' in 05:44 PM)
        ':': ":",
        // the first day of the week (0 = Sunday, 1 = Monday, etc)
        firstDay: 0,
        days: {
            // full day names
            names: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            // abbreviated day names
            namesAbbr: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
            // shortest day names
            namesShort: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"]
        },
        months: {
            // full month names (13 months for lunar calendards -- 13th month should be "" if not lunar)
            names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre", ""],
            // abbreviated month names
            namesAbbr: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic", ""]
        },
        // AM and PM designators in one of these forms:
        // The usual view, and the upper and lower case versions
        //      [standard,lowercase,uppercase]
        // The culture does not use AM or PM (likely all standard date formats use 24 hour time)
        //      null
        AM: ["AM", "am", "AM"],
        PM: ["PM", "pm", "PM"],
        eras: [
            // eras in reverse chronological order.
            // name: the name of the era in this culture (e.g. A.D., C.E.)
            // start: when the era starts in ticks (gregorian, gmt), null if it is the earliest supported era.
            // offset: offset in years from gregorian calendar
            { "name": "A.D.", "start": null, "offset": 0 }
        ],
        twoDigitYearMax: 2029,
        patterns: {
            // short date pattern
            d: "d/M/yyyy",
            // long date pattern
            D: "dddd, MMMM dd, yyyy",
            // short time pattern
            t: "h:mm tt",
            // long time pattern
            T: "h:mm:ss tt",
            // long date, short time pattern
            f: "dddd, dd MMMM, yyyy h:mm tt",
            // long date, long time pattern
            F: "dddd, dd MMMM, yyyy h:mm:ss tt",
            // month/day pattern
            M: "dd MMMM",
            // month/year pattern
            Y: "MMMM yyyy",
            // S is a sortable format that does not vary by culture
            S: "yyyy\u0027-\u0027MM\u0027-\u0027dd\u0027T\u0027HH\u0027:\u0027mm\u0027:\u0027ss",
            // formatting of dates in MySQL DataBases
            ISO: "yyyy-MM-dd hh:mm:ss",
            ISO2: "yyyy-MM-dd HH:mm:ss",
            d1: "dd.MM.yyyy",
            d2: "dd-MM-yyyy",
            d3: "dd-MMMM-yyyy",
            d4: "dd-MM-yy",
            d5: "H:mm",
            d6: "HH:mm",
            d7: "HH:mm tt",
            d8: "dd/MMMM/yyyy",
            d9: "MMMM-dd",
            d10: "MM-dd",
            d11: "MM-dd-yyyy"
        },
        percentsymbol: "%",
        currencysymbol: "$",
        currencysymbolposition: "before",
        decimalseparator: '.',
        thousandsseparator: ',',
        pagergotopagestring: "Ir a:",
        pagershowrowsstring: "Mostrar filas:",
        pagerrangestring: " de ",
        pagerpreviousbuttonstring: "anterior",
        pagernextbuttonstring: "siguiente",
        pagerfirstbuttonstring: "primero",
        pagerlastbuttonstring: "último",
        groupsheaderstring: "Arrastre una columna y suéltela aquí para agruparla en esa columna",
        sortascendingstring: "Ordenar ascendente",
        sortdescendingstring: "Ordenar descendente",
        sortremovestring: "Eliminar ordenar",
        groupbystring: "Grupo por esta columna",
        groupremovestring: "Eliminar de grupos",
        filterclearstring: "Limpiar",
        filterstring: "Filtrar",
        filtershowrowstring: "Mostrar filas donde:",
        filterorconditionstring: "O",
        filterandconditionstring: "Y",
        filterselectallstring: "(Todo)",
        filterchoosestring: "Por favor elige",
        filterstringcomparisonoperators: ['vacío', 'no vacío', 'contiene', 'contiene (distingue mayus.)',
            'no contiene', 'no contiene (distingue mayus.)', 'comienza con', 'comienza con (distingue mayus.)',
            'termina con', 'termina con (distingue mayus.)', 'igual', 'igual (distingue mayus.)', 'nulo', 'no nulo'],
        filternumericcomparisonoperators: ['igual', 'no igual', 'menor que', 'menor o igual', 'mayor que', 'mayor o igual', 'nulo', 'no nulo'],
        filterdatecomparisonoperators: ['igual', 'no igual', 'menor que', 'menor o igual', 'mayor que', 'mayor o igual', 'nulo', 'no nulo'],
        filterbooleancomparisonoperators: ['igual', 'no igual'],
        validationstring: "El valor ingresado no es válido",
        emptydatastring: "No hay datos para mostrar",
        filterselectstring: "Seleccionar filtro",
        loadtext: "Cargando...",
        clearstring: "Limpiar",
        todaystring: "Hoy",
        filtersearchstring: "Buscar:"
    }
