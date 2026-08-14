<div class="item">
    <h6 class="sub-title">CONDICIÓN DE LA INNOVACIÓN</h6>
    <img class="scoring-3"
        src='https://quickchart.io/chart?c={
            type: "radar",
            data: {
                labels: ["C1", "C2", "C3", "C4", "C5", "C6", "C7","C8","C9", "C10"],
                datasets: [{
                    label: "Puntaje (Sigla)",
                    data: [
                        {{ $answers_eval[16]['puntaje'] }},
                        {{ $answers_eval[17]['puntaje'] }},
                        {{ $answers_eval[18]['puntaje'] }},
                        {{ $answers_eval[19]['puntaje'] }},
                        {{ $answers_eval[20]['puntaje'] }},
                        {{ $answers_eval[21]['puntaje'] }},
                        {{ $answers_eval[22]['puntaje'] }},
                        {{ $answers_eval[23]['puntaje'] }},
                        {{ $answers_eval[24]['puntaje'] }},
                        {{ $answers_eval[25]['puntaje'] }},
                    ],
                    backgroundColor: "rgba(146, 184, 69, 0.38)",
                    borderColor: "rgb(146, 184, 69)",
                    pointBackgroundColor: "rgb(146, 184, 69)",
                    pointBorderColor: "rgb(146, 184, 69)",
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
        alt="scoring_condicion">
</div>

