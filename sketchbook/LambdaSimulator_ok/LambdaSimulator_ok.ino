/***************************************************************
 * Lambdasondensimmulator: gibt an Pin 6 ein PWM-Signal aus
 * Das Signal schwankt im Idealfall zwichen 800mV und 900mV
 * Die interne LED an Pin 13 dient als Statusanzeige.
 * written by Ronny Kumke ronny@inter-data.de
 ****************************************************************/

int led     = 13;  
int signal  = 6;
int timeOn  = 1000;
int timeOff = 1000;
int u_low   = 70; // =0,72V
int u_high  = 73; // =0,76V
//Mit Masseversatz 85,88

void setup() {                

  pinMode(led, OUTPUT);
  pinMode(signal, OUTPUT);  
}


void loop() {

  digitalWrite(led, HIGH);   
  analogWrite(signal, u_high);
  delay(timeOn);
  digitalWrite(led, LOW);    
  analogWrite(signal, u_low);
  delay(timeOff);              
}

