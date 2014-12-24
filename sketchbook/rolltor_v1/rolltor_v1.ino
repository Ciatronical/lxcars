/* Rolltor soll t=50sec geöffnet sein. Wenn man hindurchfährt */ 
/* t1>1,5sec soll das Tor sofort schließen                    */

int inPin = 6;  //Pin 6 erhält Signal von Lichtschranke
int outPin1 = 4; //Pin 4 gibt Signal zum Öffnen
int outPin2 = 13; //Kontroll-LED zu Pin6
boolean inVal = 0;    //Variable zum Speichern des Eingangswertes
boolean outVal = 0; //Variable Ausgabewert

void setup(){
  pinMode(inPin, INPUT);    //Pin 4 als Eingangs-Pin
  pinMode(outPin1, OUTPUT);  //Pin 6 als Ausgangs-Pin
  pinMode(outPin2, OUTPUT);
}

void loop(){
  /*inVal = digitalRead(inPin);
  outVal = !inVal;
  digitalWrite(outPin1, outVal);
  digitalWrite(outPin2, outVal);*/
  
  inVal = digitalRead(inPin); // lesen des Eingangspins


}
