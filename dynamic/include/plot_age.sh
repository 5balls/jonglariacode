#!/usr/bin/gnuplot -c
set title "Altersverteilung Teilnehmer"
set ylabel "Anzahl Teilnehmer"
set xlabel "Alter"
set locale 'de_DE.UTF-8'
set tics front
set key opaque
set datafile separator whitespace
set terminal unknown
set boxwidth 3
plot '<cat -' using 1:2 with boxes fs solid title "Bestätigte Anmeldungen"
set terminal pngcairo size 1600,800 fontscale 2
refresh
