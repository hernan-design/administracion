<div class="item">
    <h6 class="sub-title">FOMENTO A LA INNOVACIÓN</h6>
    <img class="scoring-2"
        src='https://quickchart.io/chart?c={
            type: "radar",
            data: {
                labels: ["C1", "C2", "C3", "C4", "C5", "C6", "C7","C8","C9", "C10"],
                datasets: [{
                    label: "Puntaje (Sigla)",
                    data: [
                        {{ $answers_eval[6]['puntaje'] }},
                        {{ $answers_eval[7]['puntaje'] }},
                        {{ $answers_eval[8]['puntaje'] }},
                        {{ $answers_eval[9]['puntaje'] }},
                        {{ $answers_eval[10]['puntaje'] }},
                        {{ $answers_eval[11]['puntaje'] }},
                        {{ $answers_eval[12]['puntaje'] }},
                        {{ $answers_eval[13]['puntaje'] }},
                        {{ $answers_eval[14]['puntaje'] }},
                        {{ $answers_eval[15]['puntaje'] }},
                    ],
                    backgroundColor: "rgba(189, 136, 136, 0.38)",
                    borderColor: "rgb(209, 48, 47)",
                    pointBackgroundColor: "rgb(209, 48, 47)",
                    pointBorderColor: "rgb(209, 48, 47)",
                    pointHoverBackgroundColor: "black",
                    pointHoverBorderColor: "black",
                    borderWidth: 2,
                    pointRadius: 5
                },]
            },
            options: {
                legend: {
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        fontSize: 12,
                        padding: 30,
                        boxWidth: 15
                    }
                },
                scale: {
                    ticks: {
                        beginAtZero: true
                    },
                    pointLabels: {
                        fontSize: 12
                    },
                    grid: {
                        color: "black"
                    },
                    angleLines: {
                        color: "black"
                    },
                }
            }
        }'
        alt="scoring_fomento">
</div>

