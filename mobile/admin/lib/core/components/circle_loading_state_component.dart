import 'package:flutter/material.dart';

class CircleLoadingStateComponent extends StatelessWidget {
  const CircleLoadingStateComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: CircularProgressIndicator(color: Color.fromRGBO(245, 178, 238, 1)),
    );
  }
}
