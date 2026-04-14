import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/payments_to_student/presentation/view/widgets/custom_payments_view_body.dart';

class PaymentsView extends StatelessWidget {
  const PaymentsView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomPaymentsViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomPaymentsViewBody());
    }
  }
}
