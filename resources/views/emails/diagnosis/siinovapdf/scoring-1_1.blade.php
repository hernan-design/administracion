<div class="item">
    <h6 class="sub-title">INNOVACIÓN CONCEPTUAL Y FOCALIZADA</h6>
    <img class="scoring-1"
        src='https://quickchart.io/chart?c={
            type: "radar",
            data: {
                labels: ["C1", "C2", "C3", "C4", "C5", "C6"],
                datasets: [{
                    label: "Puntaje (Sigla)",
                    data: [
                        {{ $answers_eval[0]['puntaje'] }},
                        {{ $answers_eval[1]['puntaje'] }},
                        {{ $answers_eval[2]['puntaje'] }},
                        {{ $answers_eval[3]['puntaje'] }},
                        {{ $answers_eval[4]['puntaje'] }},
                        {{ $answers_eval[5]['puntaje'] }},
                    ],
                    backgroundColor: "rgba(62,182,255,0.38)",
                    borderColor: "rgb(39, 167, 221)",
                    pointBackgroundColor: "rgb(39, 167, 221)",
                    pointBorderColor: "rgb(39, 167, 221)",
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
    alt="scoring_innovacion">
</div>

