function updateSingleFinanceLabel() {
    if (typeof(p4lsettings.products) != 'undefined' && $('onlyone_financeproduct') != null) {
        for (var x in p4lsettings.products){
            product1 = p4lsettings.products[x];
            $('onlyone_financeproduct').update(product1.productname);
            $('p4l_calcElements_term').value = product1.term;
            $('p4l_calcElements_productname').value = product1.productname;
        }
    }
}

function updateFinanceOptionSelection(){

    var numberofproductscounter = 0;
    for (var pdcts in p4lsettings.products) {
        numberofproductscounter++;
    }

    if(numberofproductscounter > 1){

        // we need to look through our JSON object for the given product.
        var selected = $('p4l_financeproduct').options[$('p4l_financeproduct').selectedIndex].value;
        for (var x in p4lsettings.products){
            var product = p4lsettings.products[x];
            if(product.productid == selected){
                $('p4l_calcElements_term').value = product.term;
                $('p4l_calcElements_productname').value = product.productname;
            }
        }
    }else{
        for (var x in p4lsettings.products){
            var product = p4lsettings.products[x];
            $('p4l_calcElements_term').value = product.term;
            $('p4l_calcElements_productname').value = product.productname;
        }
    }
    recalculate();
}

function recalculate(){
    var numberofproductscounter = 0;
    for (var pdcts in p4lsettings.products) {
        numberofproductscounter++;
    }

    if(numberofproductscounter > 1) {
        producttype = $('p4l_financeproduct').options[$('p4l_financeproduct').selectedIndex].value;
    } else {
        for (var x in p4lsettings.products){
            product = p4lsettings.products[x];
            producttype = product.productid;
        }
    }
    my_fd_obj = new FinanceCalc(producttype, parseFloat($('p4l_ordertotal').value), parseFloat($('p4l_calcElements_deposit').value),parseFloat(0));
    $('p4l_monthlies').innerHTML = my_fd_obj.m_inst;
    $('p4l_depositpayable').innerHTML = my_fd_obj.d_amount;
    $('p4l_totalpayable').innerHTML = my_fd_obj.total;
    $('p4l_monthcount').innerHTML = $('p4l_calcElements_term').value;
    if(parseFloat(DEPOSIT_INFLATION) > 0){
        $('depositInflation').innerHTML = roundNumber(parseFloat(DEPOSIT_INFLATION),2);
        $('depositInflator').appear();
    }else{
        $('depositInflator').hide();
    }
}

function roundNumber(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
    return result;
}

function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

function explode(delimiter, string, limit) {
    var emptyArray = {
        0: ''
    };

    // third argument is not required
    if ( arguments.length < 2 ||
        typeof arguments[0] == 'undefined' ||
        typeof arguments[1] == 'undefined' ) {
        return null;
    }

    if ( delimiter === '' ||
        delimiter === false ||
        delimiter === null ) {
        return false;
    }

    if ( typeof delimiter == 'function' ||
        typeof delimiter == 'object' ||
        typeof string == 'function' ||
        typeof string == 'object' ) {
        return emptyArray;
    }

    if ( delimiter === true ) {
        delimiter = '1';
    }

    if (!limit) {
        return string.toString().split(delimiter.toString());
    } else {
        // support for limit argument
        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;
    }
}

function strrev (string) {
    string = string+'';
    return string.split('').reverse().join('');
}

function selectionUnavailable() {
    if($('p_method_p4lpayment').checked){
        alert('Sorry, Finance is not available on this order');
        $('p_method_p4lpayment').checked = '';
    }
}