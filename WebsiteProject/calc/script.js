function multiplyBy() {
    let firstNumber = document.getElementById("firstNumber").value;
    let secondNumber = document.getElementById("secondNumber").value;
    let result = firstNumber * secondNumber;
    document.getElementById("result").textContent = result;
}

function divideBy() {
    let firstNumber = document.getElementById("firstNumber").value;
    let secondNumber = document.getElementById("secondNumber").value;

    if (secondNumber == 0) {
        document.getElementById("result").textContent = "Cannot divide by zero";
    } else {
        let result = firstNumber / secondNumber;
        document.getElementById("result").textContent = result;
    }
}

function addBy() {
    let firstNumber = parseFloat(document.getElementById("firstNumber").value);
    let secondNumber = parseFloat(document.getElementById("secondNumber").value);
    let result = firstNumber + secondNumber;
    document.getElementById("result").textContent = result;
}

function subBy() {
    let firstNumber = parseFloat(document.getElementById("firstNumber").value);
    let secondNumber = parseFloat(document.getElementById("secondNumber").value);
    let result = firstNumber - secondNumber;
    document.getElementById("result").textContent = result;
}

function modBy() {
    let firstNumber = document.getElementById("firstNumber").value;
    let secondNumber = document.getElementById("secondNumber").value;
    let result = firstNumber % secondNumber;
    document.getElementById("result").textContent = result;
}
// color pallete https://i.pinimg.com/564x/23/aa/44/23aa4435c9fab597ec20d625b87aa1a1.jpg