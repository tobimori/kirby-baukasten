import confetti from 'canvas-confetti'

confetti.create(document.querySelector('#canvas'), {
  resize: true,
  useWorker: true,
})({ particleCount: 200, spread: 200 })
