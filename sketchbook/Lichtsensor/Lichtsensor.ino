/****************************************************************************************
*** Schaltet die Außenbeleuchtung von Auto-Spar Zeit- und Dämmerungsabhängig          ***
*** Dient gleichzeitig als Uhr, dabei werden die Stunden durch langes Blinken         ***
*** signalisiert.                                                                      ***
****************************************************************************************/

#include <Wire.h>
#include "RTClib.h"


// Initialisierung
const int analogInPin = A0;    // Eingang des Sensors
const int relaisPin   = 13;    // Ausgang für Relais
int schwelle    = 500;         // Schwellwert für Dunkelheit
int sensorValue = 0;           // Intit des Variablen die den Messwert aufnimmt
bool dunkel     = false;
bool debug      = 1;       // Debugging on / off
int hourOn      = 17;
int hourOffWeek = 0;          // So,Mo,Di,Mi,Do,
int hourOffWE   = 2;           // Fr,Sa
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


  if( now.minute()%2 == 0 && now.second() == 0 ){
    if( debug ){
       Serial.print(" Jetzt!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!: \n");

    delay(950);
    sensorValue = analogRead(analogInPin);
    if( sensorValue < schwelle ){
      //dunkel = true;
      digitalWrite(relaisPin,HIGH);
      delay(3000);
      //short i = now.hour();
      // Stunden
    }
    else{  // sensorValue > schwelle (es ist hell...)
      //dunkel = false;
      digitalWrite(relaisPin,LOW);
    }
  }

  if( debug ){
    Serial.print(" SensorValue: ");
    Serial.print(sensorValue);
    Serial.print("\n");
    delay(700);
  }


}


/*
      while( i > 0 ){
        digitalWrite(relaisPin,HIGH);
        delay( 1200 );
        digitalWrite(relaisPin,LOW);
        delay( 700 );
        i--;
      }

      delay( 2000 );
      //Wochenende
      if( now.dayOfWeek() == 6 || now.dayOfWeek() == 7 ){
        if (  now.hour() > 16 || now.hour() < 3 ){
          digitalWrite(relaisPin,HIGH);
        }
        else digitalWrite(relaisPin,LOW);
      }
      else{  //innerhalb der Woche
        if (  now.hour() > 16 || now.hour() < 1 ){
          digitalWrite(relaisPin,HIGH);
        }
        else digitalWrite(relaisPin,LOW);
      }
    }

    */
