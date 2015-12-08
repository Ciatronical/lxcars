#include <Wire.h>
#include "RTClib.h"

const int analogInPin      = A0;//Lichtsensor
const short switchPin2     = 2;
const short switchPin3     = 3;
const short switchPin4     = 4;
const short switchPin6     = 6;

const short U1Pin     = 1;
const short UgPin     = 2;
//const short xxx = 3;
const short pwmOutPin = 5;
const short UladePin     = 7;

const short outPin8_0    = 8;// Arduino_MOSFET von re
const short outPin9_1    = 9;
const short outPin10_2    = 10;
const short outPin11_3    = 11;
const short outPin12_4    = 12;
const short outPin13_5    = 13;
long Ugi = 0;
long U1i = 0;
float UgMittel = 0;
float U1Mittel = 0;
//int Uin = 0;
int Ug = 0;
int U1 = 0;
int U2 = 0;
int PWM = 0 ;
int i = 0;
short Usoll = 450;
short Umin = 425;

//Lichtsensor Init
int schwelle    = 500;         // Schwellwert für Dunkelheit
int sensorValue = 0;           // Intit des Variablen die den Messwert aufnimmt

//Init RTC
RTC_DS1307 RTC;

bool debug = 0;

bool ladeState = LOW;
bool switch2 = LOW;
bool switch3 = LOW;
bool switch4 = LOW;
bool switch6 = LOW;
unsigned long currentMillis = 0;
long previousMillis = 0;
long interval       = 10000;
long ladeInterval   = 1000*60*60;//eine stunde
void setup(){
  if( debug ) Serial.begin(9600);
  pinMode( pwmOutPin, OUTPUT );
  pinMode( outPin8_0, OUTPUT );
  pinMode( outPin9_1, OUTPUT );
  pinMode( outPin10_2, OUTPUT );
  pinMode( outPin11_3, OUTPUT );
  pinMode( outPin12_4, OUTPUT );
  pinMode( outPin13_5, OUTPUT );
  pinMode( UladePin, OUTPUT );
  pinMode( switchPin2, INPUT_PULLUP );
  pinMode( switchPin3, INPUT_PULLUP );
  pinMode( switchPin4, INPUT_PULLUP );
  pinMode( switchPin6, INPUT_PULLUP );
  digitalWrite( UladePin, LOW );
   //setPwmFrequency(10, 10);

  Wire.begin();
  RTC.begin();

  if ( !RTC.isrunning() ){
    Serial.println("RTC is NOT running!");
    //following line sets the RTC to the date & time this sketch was compiled
  }
  RTC.adjust(DateTime(__DATE__, __TIME__));


}
void loop() {
  currentMillis = millis();
  DateTime now = RTC.now();



 /**********************************************************************************
 +++ Spannungsstabilisator
 **********************************************************************************/

  Ugi += analogRead( UgPin );
  U1i += analogRead( U1Pin );
  if( i == 50 ){
    UgMittel = Ugi / i;
    U1Mittel = U1i / i;
    U2 = UgMittel - U1Mittel;
    i = 0;
    if( debug ){
      Serial.print("PWM = " );
      Serial.print(PWM);
      Serial.print("\t UgMittel = ");
      Serial.print(UgMittel);
      Serial.print("\t U1Mittel = ");
      Serial.print(U1Mittel);
      Serial.print("\t U2 = ");
      Serial.println( U2 );
    }
    Ugi = 0;
    U1i = 0;
    if( U2 <= Usoll && PWM < 255 ) PWM++;
    if( U2 > Usoll && PWM > 0 ) PWM--; //Upwm darf nicht 10 oder kleiner  sein
    analogWrite( pwmOutPin, PWM );
  }
  i++;

  /************************************************************************************
  +++ Ladeschalter
  *************************************************************************************/
  if( UgMittel <= Umin ){
    digitalWrite( UladePin, HIGH );
    previousMillis = currentMillis;
  }
  if( currentMillis - previousMillis > ladeInterval && UgMittel > Umin){//jetzt messen
     digitalWrite( UladePin, LOW );

    //previousMillis = currentMillis;
    //if( UgMittel <= Umin ) digitalWrite( UladePin, HIGH );
     //digitalWrite( UladePin, LOW );

  }

  /************************************************************************************
  +++ Lichtschalter
  *************************************************************************************/
  switch2 = digitalRead( switchPin2);
  switch3 = digitalRead( switchPin3);
  switch4 = digitalRead( switchPin4);
  switch6 = digitalRead( switchPin6);

  digitalWrite( outPin8_0,  !switch6 );
  digitalWrite( outPin9_1,  !switch6 );
  digitalWrite( outPin10_2, !switch6 );
  digitalWrite( outPin11_3, !switch4 );
  digitalWrite( outPin12_4, !switch3 );
  //digitalWrite( outPin13_5, switch2 );//Aussenbeleuchtung.
  //digitalWrite( outTstPin, 1 );

  /************************************************************************************
  +++ Uhr
  ************************************************************************************/
  if( debug ){
    Serial.print(now.year(), DEC);
    Serial.print('/');
    Serial.print(now.month(), DEC);
    Serial.print('/');
    Serial.print(now.day(), DEC);
    Serial.print(' ');
    Serial.print(now.hour(), DEC);
    Serial.print(':');
    Serial.print(now.minute(), DEC);
    Serial.print(':');
    Serial.print(now.second(), DEC);
    Serial.print(" dayOfWeek: ");
    Serial.print(now.dayOfWeek(), DEC);
    Serial.println();
  }


  //if( now.hour() >= 16 && now.hour() < 23 && now.day() > 4  ) digitalWrite( outPin13_5, HIGH );
  //else digitalWrite( outPin13_5, LOW );
  if( now.day() >= 1 && now.day() >= 4 ){ //Montag bis Donnerstag
    if( now.hour() == 18 ) digitalWrite( outPin13_5, HIGH );
    if( now.hour() == 23 ) digitalWrite( outPin13_5, LOW );
  }

  if( now.day() == 5 ){ //am Freitag wird das Licht nur eingeschaltet
    if( now.hour() == 18 ) digitalWrite( outPin13_5, HIGH );
  }
  if( now.day() == 6 ){ //Sonnabend
    if( now.hour() == 2 ) digitalWrite( outPin13_5, LOW );
    if( now.hour() == 19 ) digitalWrite( outPin13_5, HIGH );
  }
  if( now.day() == 7 ){ //Sonntag
    if( now.hour() == 2 || now.hour() == 23 ) digitalWrite( outPin13_5, LOW );
    if( now.hour() == 19 ) digitalWrite( outPin13_5, HIGH );
  }
}
