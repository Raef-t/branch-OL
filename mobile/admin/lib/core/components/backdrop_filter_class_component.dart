import 'dart:ui';
import 'package:flutter/material.dart';
import '/core/border_radius/circulars.dart';

class BackdropFilterClassComponent extends StatelessWidget {
  const BackdropFilterClassComponent({super.key, required this.child});
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: Circulars.circular72(context: context),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 7, sigmaY: 7),
        child: child,
      ),
    );
  }
}
