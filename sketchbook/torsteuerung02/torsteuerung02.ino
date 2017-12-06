short analogPinLaserLeft    = 0;     
short analogPinLaserRight   = 1; 
short analogPinLaserLeftOutdoor  = 2;
short analogPinLaserRightOutdoor = 3;
short digitalPinGateLevel   = 4; 
short digitalPinGateOpen    = 6; 
unsigned long timeOpen      = millis(); 
bool isOpen                 = 0;               

int valLaserLeft  = 0;  
int valLaserRight = 0;
int valLaserLeftOutdoor  = 0;  
int valLaserRightOutdoor = 0;
int n   = 0;
int averageLaserLeft  = 0;
int averageLaserRight = 0;
int averageLaserLeftOutdoor  = 0;
int averageLaserRightOutdoor = 0;
int limitLaserLeft    = 659; //609 = 80cm
int limitLaserRight   = 659; //609 = 80cm
int limitLaserLeftOutdoor    = 659; //609 = 80cm
int limitLaserRightOutdoor   = 659; //609 = 80cm
long sumLaserLeft  = 0;
long sumLaserRight = 0;
long sumLaserLeftOutdoor  = 0;
long sumLaserRightOutdoor = 0;
int i    = 0;
int max = 0;
int min = 1000;
bool debug = 0 ;
bool dontOpen = 0;
void setup(){
  pinMode( digitalPinGateOpen,  OUTPUT );
  pinMode( digitalPinGateLevel, OUTPUT ); 
  if( debug) Serial.begin( 9600 );  
    
}

void loop(){
  valLaserLeft  = analogRead( analogPinLaserLeft );
  valLaserRight = analogRead( analogPinLaserRight );
  valLaserLeftOutdoor  = analogRead( analogPinLaserLeftOutdoor );
  valLaserRightOutdoor = analogRead( analogPinLaserRightOutdoor );
  i++;
  sumLaserLeft  += valLaserLeft;
  sumLaserRight += valLaserRight;
  sumLaserLeftOutdoor  += valLaserLeftOutdoor;
  sumLaserRightOutdoor += valLaserRightOutdoor;
  if( debug ){
    if( valLaserLeft >= max ) max = valLaserLeft;
    if( valLaserLeft <= min ) min = valLaserLeft;
  }
  
  

  if( i == 500 ){
    averageLaserLeft  = sumLaserLeft  / i;
    averageLaserRight = sumLaserRight / i;
    averageLaserLeftOutdoor  = sumLaserLeftOutdoor  / i;
    averageLaserRightOutdoor = sumLaserRightOutdoor / i;
    if( debug ){
      Serial.print("Letzer Messwert: ");
      Serial.println( valLaserLeft );
      Serial.print("averageWERT: ");
      Serial.println( averageLaserLeft );
      Serial.print("MaxWERT: ");
      Serial.println( max );
      Serial.print("MinWERT: ");
      Serial.println( min );
      max = 0;
      min = 1000;
    }
    if( abs( averageLaserLeft - averageLaserRight ) > 100 ){
      delay( 3000 );  
    }      
    if( averageLaserLeft > limitLaserLeft  &&  averageLaserRight > limitLaserRight && timeOpen + 80000 <= millis()){
      digitalWrite( digitalPinGateOpen, HIGH );
      timeOpen = millis();
      delay( 500 );
      digitalWrite( digitalPinGateOpen, LOW ); 
      isOpen = true;
    }
    if( timeOpen + 5500 >= millis() && averageLaserLeft > limitLaserLeft - 150  &&  averageLaserRight > limitLaserRight -150 && isOpen ){
      digitalWrite( digitalPinGateLevel, HIGH );
      delay( 3000 );
      digitalWrite( digitalPinGateLevel, LOW );
    }/*
    if( averageLaserLeftOutdoor > limitLaserLeftOutdoor  &&  averageLaserRightOutdoor > limitLaserRightOutdoor && timeOpen + 80000 <= millis()){
      //digitalWrite( digitalPinGateOpen, HIGH );
      timeOpen = millis();
      delay( 500 );
      digitalWrite( digitalPinGateOpen, LOW ); 
      isOpen = true;
    }
    if( timeOpen + 5500 >= millis() && averageLaserLeftOutdoor > limitLaserLeftOutdoor - 150  &&  averageLaserRightOutdoor > limitLaserRightOutdoor -150 && isOpen ){
      digitalWrite( digitalPinGateLevel, HIGH );
      delay( 3000 );
      digitalWrite( digitalPinGateLevel, LOW );
    }*/
    isOpen = false;
    sumLaserLeft  = 0;
    sumLaserRight = 0;
    i = 0;
    
    //delay( 300 );
  } 
}
