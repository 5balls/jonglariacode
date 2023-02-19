#!/usr/bin/gnuplot -c
set title "Zeitverlauf Anmeldungen"
set ylabel "Gesamtzahl Anmeldungen"
set xlabel "Datum"
set xdata time
set timefmt "%s"
set format x "%d. %B"
set xtics rotate by 45 right
set locale 'de_DE.UTF-8'
set tics front
set key opaque
set datafile separator whitespace
set terminal unknown
plot '<cat -' using (lastx=$1):(lasty=$2) with filledcurve x1 title "Bestätigte Anmeldungen"
set terminal pngcairo size 1600,800 fontscale 2
set label sprintf("%g", lasty) at lastx,lasty
set xrange [:lastx]
refresh
