/* Rolltor soll t=50sec geöffnet sein. Wenn man hindurchfährt */ 
/* t1>1,5sec soll das Tor sofort schließen                    */

int inputPin = A0;
int outputPin = 4;
int time1 = 0;
int time2 = 0;
boolean zustand = 0;

void setup () {
  pinMode(outputPin, OUTPUT);
  Serial.begin(9600);
}

void loop() {
  if(inputPin == HIGH){  //wenn Lichtschranke geschlossen dann Zustand 1
    zustand=1;           
  }
  else{                  //wenn Lichtschranke unterbrochen Zustand 0
    zustand=0;
  }
  
  if (zustand == 1){
    if (zustand == 0){
      time1 = millis();    //Zeit1 nehmen wenn Lichtschranke unterbrochen wird
    }
  }
  if (zustand == 0){
    if (zustand == 1){
      time2 = millis();
    }  
  }
  if (time2-time1>1499){
    digitalWrite(outputPin, HIGH);
    delay(500);
    digitalWrite(outputPin, LOW);
  } 
  
}
