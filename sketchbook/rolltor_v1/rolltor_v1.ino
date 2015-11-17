/* Rolltor soll t=50sec geöffnet sein. Wenn man hindurchfährt */
/* t1>1,5sec soll das Tor sofort schließen sobald die         */
/* Lichtschranke wieder geschlossen ist                       */

int inPin = 6;  //Pin 6 erhält Signal von Lichtschranke
int outPin1 = 4; //Pin 4 gibt Signal zum Öffnen
int ledPin = 13; //Kontroll-LED zu Pin6
unsigned long time1;
unsigned long time2;
byte status = 1;
//boolean inVal = 0;    //Variable zum Speichern des Eingangswertes
//boolean outVal = 0; //Variable Ausgabewert

void setup(){
  pinMode(inPin, INPUT);    //Pin 4 als Eingangs-Pin
  pinMode(outPin1, OUTPUT);  //Pin 6 als Ausgangs-Pin
  pinMode(ledPin, OUTPUT);  //Kontroll-LED
  Serial.begin(9600);

}

void loop(){
  /*inVal = digitalRead(inPin);
  outVal = !inVal;
  digitalWrite(outPin1, outVal);
  digitalWrite(ledPin, outVal);*/

  digitalRead(inPin); // lesen des Eingangspins
  status = 0;

  if( digitalRead( inPin ) == LOW && status == 0){
    time1 = millis();
    status = 1;
  }


  if( digitalRead( inPin ) == HIGH ){
    time2 = millis();
  }

  if (time2 - time1 > 1500 && digitalRead(inPin)==1){
    digitalWrite(outPin1, HIGH);
    digitalWrite(ledPin, HIGH);
    delay(100);
    digitalWrite(outPin1, LOW);
    digitalWrite(ledPin, LOW);

  }


}
