


jQuery(function ($) {




let loader ='<svg id="loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: rgb(255, 255, 255) none repeat scroll 0% 0%; display: block; shape-rendering: auto;"  viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n' +
    '<path d="M14 50A36 36 0 0 0 86 50A36 39.4 0 0 1 14 50" fill="#ff6233" stroke="none">\n' +
    '  <animateTransform attributeName="transform" type="rotate" dur="0.25316455696202533s" repeatCount="indefinite" keyTimes="0;1" values="0 50 51.7;360 50 51.7"></animateTransform>\n' +
    '</path>\n' +
    ' </svg>';



        $('.re_affiliate_container').submit(function (e) {
            e.preventDefault();
            $('.header').append(loader);
            let re_first_name = $(this).find('.re_first_name').val();
            let re_last_name = $(this).find('.re_last_name').val();
            let re_add_link = $(this).find('.re_add_link').val();
            let re_add_app = $(this).find('.re_add_app').val();
            let re_how_find = $(this).find('.re_how_fin').val();
            let re_cart_number = $(this).find('.account_cart_number').val();
            let re_cart_name = $(this).find('.account_cart_name').val();

           let admin_url = re_affiliate.admin_url;

            $.ajax({
                url : admin_url ,
                method : 'POST' ,
                data:{
                    'action'          : 're_affiliate' ,
                    're_first_name'   : re_first_name  ,
                    're_last_name'    : re_last_name   ,
                    're_add_link'     : re_add_link    ,
                    're_add_app'      : re_add_app     ,
                    're_how_find'     : re_how_find    ,
                    're_cart_number'  : re_cart_number   ,
                    're_cart_name'    : re_cart_name     ,
                } ,
                success:function (e) {

                   $('.header').hide();
                   location.reload();

                },
                error:function (x , a  , t ) {
                }
            });

    });





    let label = $('#line').data('datalable') ;
    if (   label != null  ) {
        label = label.replace(/_/g, '/');
        label = label.split(",");

        let value = $('#line').data('datavalue');
        value = value.replace(/_/g, '/');
        value = value.split(",");


        /*=========================================
        Multiple Line Chart
        ===========================================*/
        var line = document.getElementById("line").getContext("2d");

        var gradientFill = line.createLinearGradient(0, 120, 0, 0);
        gradientFill.addColorStop(0, "rgba(41,204,151,0.10196)");
        gradientFill.addColorStop(1, "rgba(41,204,151,0.30196)");

        var lChart = new Chart(line, {
            type: 'line',
            data: {
                labels: label,
                datasets: [{
                    label: "My First dataset",
                    lineTension: 0.2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(255,255,255,1)',
                    pointBorderWidth: 2,
                    fill: true,
                    backgroundColor: gradientFill,
                    borderColor: '#29cc97',
                    borderWidth: 2,
                    data: value
                }]
            },
            options: {
                responsive: true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            drawBorder: true,
                            display: true
                        },
                        ticks: {
                            display: true, // hide main x-axis line
                            beginAtZero: true
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            drawBorder: true, // hide main y-axis line
                            display: true
                        },
                        ticks: {
                            display: true,
                            beginAtZero: true,
                        },
                    }]
                },
                tooltips: {
                    enabled: true
                }
            }
        });

    }

    $(document).on('click' , '.copy-token' , function () {
        let text = $(this).val();
        let input = document.createElement('input');
        input.setAttribute('value', text);
        document.body.appendChild(input);
        input.select();
        let result = document.execCommand('copy');
        document.body.removeChild(input);
        alert("لینک گپی شد");
        return result;
    })

    // $(document).on('click' , '.copy-token' , function () {
    //     let text = 'zff';
    //     window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
    //
    // })

})