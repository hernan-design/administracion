<div class="item">
    <h6 class="sub-title">INNOVACIÓN COLABORATIVA</h6>
    <img class="scoring-4"
        src='https://quickchart.io/chart?c={
            type: "radar",
            data: {
                labels: ["C1", "C2", "C3", "C4"],
                datasets: [{
                    label: "Puntaje (Sigla)",
                    data: [
                        {{ $answers_eval[26]['puntaje'] }},
                        {{ $answers_eval[27]['puntaje'] }},
                        {{ $answers_eval[28]['puntaje'] }},
                        {{ $answers_eval[29]['puntaje'] }},
                    ],
                    backgroundColor: "rgba(255, 205, 63, 0.38)",
                    borderColor: "rgb(255, 205, 63)",
                    pointBackgroundColor: "rgb(255, 205, 63)",
                    pointBorderColor: "rgb(255, 205, 63)",
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
        alt="scoring_colaborativa">
</div>

