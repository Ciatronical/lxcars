#define trigger 9  // Arduino Pin an HC-SR04 Trig
#define echo 8     // Arduino Pin an HC-SR04 Echo
#define outPin 7

long duration = 0;
long distance = 0;
long distance_old = 0;
long add_distance = 0;
int i = 0;
long previousMillis = 0;
long interval = 120000;
long minDistance = 50;
bool debug = 0;

void setup(void) {
  pinMode( outPin, OUTPUT );
  pinMode( trigger, OUTPUT );
  pinMode( echo, INPUT );
  if( debug ) Serial.begin( 9600 );
}

void loop() {
  unsigned long currentMillis = millis();
  digitalWrite( trigger, LOW );
  delayMicroseconds( 2 );

  digitalWrite( trigger, HIGH );
  delayMicroseconds( 10 );

  digitalWrite( trigger, LOW );
  duration = pulseIn( echo, HIGH ); // Echo-Zeit messen

  // Echo-Zeit halbieren (weil hin und zurueck, der doppelte Weg ist)
  duration = ( duration / 2 );
  // Zeit des Schalls durch Luft in Zentimeter umrechnen
  distance = duration / 29.1;
  //distance = distance + distance_old;
  add_distance += distance;
  // Arithmetrisches Mittel bilden
  if( ++i == 1000 ){
    if( debug ){
      Serial.print("distance: ");
      Serial.print(distance);
      Serial.println(" cm");
      Serial.print("add_distance: ");
      Serial.print(add_distance);
      Serial.println(" cm");
    }
    add_distance /= 1000;
    i = 0;
    if( add_distance < minDistance ) {
      //Timer Starten
      previousMillis = currentMillis;
      digitalWrite( outPin, 0 );
    }
    if( currentMillis - previousMillis > interval && add_distance > minDistance ){
      digitalWrite( outPin, 1 );
      Serial.print("AUS ");
    }
    add_distance = 0;

  }
}
