/****************************************************************************************
*** Schaltet die Außenbeleuchtung von Auto-Spar Zeit- und Dämmerungsabhängig.         ***
*** Dient gleichzeitig als Uhr, dabei werden die Stunden durch Blinken signalisiert.  ***
****************************************************************************************/

#include <Wire.h>
#include "RTClib.h"


// Initialisierung
const int analogInPin = A0;    // Eingang des Sensors
const int relaisPin   = 13;    // Ausgang für Relais
int schwelle    = 500;         // Schwellwert für Dunkelheit
int sensorValue = 0;           // Intit des Variablen die den Messwert aufnimmt
bool debug      = 0;           // Debugging on / off
RTC_DS1307 RTC;                // Real Time Clock

void setup(){
  pinMode(relaisPin, OUTPUT);
  if( debug ) Serial.begin(9600);

  Wire.begin();
  RTC.begin();

  if ( !RTC.isrunning() ){
    Serial.println("RTC is NOT running!");
    // following line sets the RTC to the date & time this sketch was compiled
  }
  RTC.adjust(DateTime(__DATE__, __TIME__));
}

void loop() {
  DateTime now = RTC.now();
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


  if( now.minute()%15 == 0 && now.second() == 0 ){
    if( debug ){
       Serial.print(" Jetzt!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!: \n");
    }
    delay(800);//eine Sekunde warten dass nicht zwei mal gelessen wird
    sensorValue = analogRead(analogInPin);
    if( sensorValue < schwelle ){
      //dunkel = true;
      digitalWrite(relaisPin,HIGH);
      //delay(3000);

      /***********  begin clock ***********/
      if( now.minute() == 0 ){  //Volle Stunde
        digitalWrite( relaisPin, LOW );
        delay( 1700 );
        short i = ( now.hour() >= 12 ) ? now.hour() - 12 : now.hour();
        if( i == 0 ) i = 12;//Mitternacht
        while( i > 0 ){
          digitalWrite(relaisPin,HIGH);
          if( debug ){
            Serial.print("Blink: ");
            Serial.print( i );
            Serial.print("\n");
          }
          delay( 700 );
          digitalWrite(relaisPin,LOW);
          delay( 900 );
          i--;
        }
        delay( 1400 );
        digitalWrite( relaisPin, HIGH );
      }
      /***********  end clock  **************/

      // WE ab 2Uhr bis 6Uhr abschalten
      if( now.dayOfWeek() == 6 || now.dayOfWeek() == 7 ){ //WE
         if( now.hour() > 1 && now.hour() < 7 ){
            digitalWrite( relaisPin, LOW );
         }
      }
      // an Wochentagen
      else{
        if( now.hour() >= 0 && now.hour() < 6 ){
          digitalWrite( relaisPin,LOW );
        }
      }

    }
    else{  // sensorValue > schwelle (es ist hell...)
      //dunkel = false;
      digitalWrite( relaisPin, LOW );
    }
  }
  delay(700);

  if( debug ){
    Serial.print(" SensorValue: ");
    Serial.print(sensorValue);
    Serial.print("\n");

  }


}
